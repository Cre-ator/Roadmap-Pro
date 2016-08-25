<?php
require_once ( __DIR__ . '/rProfileManager.php' );
require_once ( __DIR__ . '/rProfile.php' );
require_once ( __DIR__ . '/rEta.php' );

/**
 * roadmap class that represents a roadmap
 *
 * @author Stefan Schwarz
 */
class roadmap
{
   /**
    * @var integer
    */
   private $versionId;
   /**
    * @var array
    */
   private $bugIds;
   /**
    * @var integer
    */
   private $profileId;
   /**
    * @var integer
    */
   private $groupId;
   /**
    * @var integer
    */
   private $doneEta;
   /**
    * @var float
    */
   private $progressPercent;
   /**
    * @var array
    */
   private $profileHashArray;
   /**
    * @var boolean
    */
   private $etaIsSet;
   /**
    * @var integer
    */
   private $singleEta;
   /**
    * @var integer
    */
   private $fullEta;
   /**
    * @var array
    */
   private $doingBugIds;
   /**
    * @var array
    */
   private $doneBugIds;
   /**
    * @var boolean
    */
   private $issueIsDone;

   /**
    * roadmap constructor.
    * @param $bugIds
    * @param $profileId
    * @param $groupId
    * @param $versionId
    */
   function __construct ( $bugIds, $profileId, $groupId, $versionId )
   {
      $this->bugIds = $bugIds;
      $this->profileId = $profileId;
      $this->groupId = $groupId;
      $this->versionId = $versionId;
      $this->doneBugIds = array ();
      $this->doingBugIds = array ();
      $this->profileHashArray = array ();
   }

   /**
    * @return bool
    */
   public function getEtaIsSet ()
   {
      $this->checkEtaIsSet ();
      return $this->etaIsSet;
   }

   /**
    * @param $bugId
    * @return int
    */
   public function getSingleEta ( $bugId )
   {
      $this->calcSingleEta ( $bugId );
      return $this->singleEta;
   }

   /**
    * @return int
    */
   public function getFullEta ()
   {
      $this->calcFullEta ();
      return $this->fullEta;
   }

   /**
    * @return array
    */
   public function getDoneBugIds ()
   {
      $this->calcDoneBugIds ();
      return $this->doneBugIds;
   }

   /**
    * @return array
    */
   public function getDoingBugIds ()
   {
      $this->calcDoingBugIds ();
      return $this->doingBugIds;
   }

   /**
    * @return int
    */
   public function getDoneEta ()
   {
      $this->calcDoneEta ();
      return $this->doneEta;
   }

   /**
    * @return float
    */
   public function getSingleProgressPercent ()
   {
      $this->calcSingleProgressPercent ();
      return $this->progressPercent;
   }

   /**
    * @param $bugId
    * @return bool
    */
   public function getIssueIsDone ( $bugId )
   {
      $this->checkIssueIsDoneById ( $bugId );
      return $this->issueIsDone;
   }

   /**
    * @return int
    */
   public function getProfileId ()
   {
      return $this->profileId;
   }

   /**
    * @return array
    */
   public function getBugIds ()
   {
      return $this->bugIds;
   }

   /**
    * @param $profileId
    */
   public function setProfileId ( $profileId )
   {
      $this->profileId = $profileId;
   }

   /**
    * new initialization of done bug ids
    */
   private function resetDoneBugIds ()
   {
      $this->doneBugIds = array ();
   }

   /**
    * @param $versionId
    */
   public function setVersionId ( $versionId )
   {
      $this->versionId = $versionId;
   }

   /**
    * @return int
    */
   public function getVersionId ()
   {
      return $this->versionId;
   }

   /**
    * @return array
    */
   public function getProfileHashArray ()
   {
      $this->calcScaledData ();
      return $this->profileHashArray;
   }

   /**
    * returns true if every item of bug id array has set eta value
    */
   private function checkEtaIsSet ()
   {
      $this->etaIsSet = true;
      if ( !config_get ( 'enable_eta' ) )
      {
         $this->etaIsSet = false;
      }
      else
      {
         foreach ( $this->bugIds as $bugId )
         {
            $bugEtaValue = bug_get_field ( $bugId, 'eta' );
            if ( ( $bugEtaValue == null ) || ( $bugEtaValue == 10 ) )
            {
               $this->etaIsSet = false;
            }
         }
      }
   }

   /**
    * returns the eta value of a bunch of bugs
    */
   private function calcFullEta ()
   {
      $this->fullEta = 0;
      foreach ( $this->bugIds as $bugId )
      {
         $bugEtaValue = bug_get_field ( $bugId, 'eta' );

         $etaEnumString = config_get ( 'eta_enum_string' );
         $etaEnumValues = MantisEnum::getValues ( $etaEnumString );

         foreach ( $etaEnumValues as $enumValue )
         {
            if ( $enumValue == $bugEtaValue )
            {
               $eta = new rEta( $enumValue );
               $this->fullEta += $eta->getEtaUser ();
            }
         }
      }
   }

   /**
    * returns the eta value of a single bug
    *
    * @param $bugId
    */
   private function calcSingleEta ( $bugId )
   {
      $bugEtaValue = bug_get_field ( $bugId, 'eta' );

      $etaEnumString = config_get ( 'eta_enum_string' );
      $etaEnumValues = MantisEnum::getValues ( $etaEnumString );

      foreach ( $etaEnumValues as $enumValue )
      {
         if ( $enumValue == $bugEtaValue )
         {
            $eta = new rEta( $enumValue );
            $this->singleEta = $eta->getEtaUser ();
         }
      }
   }

   /**
    * get the done bug ids and save them in the done bug id array
    */
   private function calcDoneBugIds ()
   {
      foreach ( $this->bugIds as $bugId )
      {
         $this->getIssueIsDone ( $bugId );
         if ( $this->issueIsDone )
         {
            array_push ( $this->doneBugIds, $bugId );
            $this->doneBugIds = array_unique ( $this->doneBugIds );
         }
      }
   }

   /**
    * get the doing bug ids and save them in the doing bug id array
    */
   private function calcDoingBugIds ()
   {
      foreach ( $this->bugIds as $bugId )
      {
         $this->getIssueIsDone ( $bugId );
         if ( $this->issueIsDone == false )
         {
            array_push ( $this->doingBugIds, $bugId );
            $this->doingBugIds = array_unique ( $this->doingBugIds );
         }
      }
   }

   /**
    * check if specified bug is done
    *
    * @param $bugId
    */
   private function checkIssueIsDoneById ( $bugId )
   {
      $this->issueIsDone = false;

      $bugStatus = bug_get_field ( $bugId, 'status' );
      $roadmapProfile = new rProfile( $this->profileId );
      $dbRaodmapStatus = $roadmapProfile->getProfileStatus ();
      $roadmapStatusArray = explode ( ';', $dbRaodmapStatus );

      foreach ( $roadmapStatusArray as $roadmapStatus )
      {
         if ( $bugStatus == $roadmapStatus )
         {
            $this->issueIsDone = true;
         }
      }
   }

   /**
    * calculate the sum of eta for all done bugs
    */
   private function calcDoneEta ()
   {
      $this->doneEta = 0;
      $doneBugIds = $this->getDoneBugIds ();
      if ( $this->getEtaIsSet () )
      {
         foreach ( $doneBugIds as $doneBugId )
         {
            $this->doneEta += $this->getSingleEta ( $doneBugId );
         }
      }
   }

   /**
    * calc the percentage of done progress for a single roadmap
    */
   private function calcSingleProgressPercent ()
   {
      $this->doneEta = 0;
      $doneBugIds = $this->getDoneBugIds ();
      if ( $this->getEtaIsSet () )
      {
         $fullEta = $this->getFullEta ();
         foreach ( $doneBugIds as $doneBugId )
         {
            $this->doneEta += $this->getSingleEta ( $doneBugId );
         }

         if ( $fullEta > 0 )
         {
            $this->progressPercent = ( ( $this->doneEta / $fullEta ) * 100 );
         }
      }
      else
      {
         $doneBugAmount = count ( $doneBugIds );
         $allBugCount = count ( $this->bugIds );
         $progress = ( $doneBugAmount / $allBugCount );
         $this->progressPercent = round ( $progress * 100 );
      }
   }

   /**
    * calc the roadmap data for a roadmap group
    */
   private function calcScaledData ()
   {
      # variables
      if ( $this->groupId == null )
      {
         $roadmapProfileIds = rProfileManager::getRProfileIds ();
      }
      else
      {
         $roadmapProfileIds = array ();
         $group = new rGroup( $this->groupId );
         $groupProfileIds = explode ( ';', $group->getGroupProfiles () );
         foreach ( $groupProfileIds as $groupProfileId )
         {
            array_push ( $roadmapProfileIds, $groupProfileId );
         }
      }
      $useEta = $this->getEtaIsSet ();
      $allBugCount = count ( $this->bugIds );
      $profileCount = count ( $roadmapProfileIds );
      $sumProfileEffort = rProfileManager::getSumRProfileEffort ( $this->groupId );

      $wholeProgress = 0;
      # iterate through profiles
      for ( $index = 0; $index < $profileCount; $index++ )
      {
         $roadmapProfileId = $roadmapProfileIds[ $index ];
         $roadmapProfile = new rProfile( $roadmapProfileId );
         $tProfileId = $roadmapProfile->getProfileId ();
         $this->setProfileId ( $tProfileId );
         # effort factor
         $tProfileEffort = $roadmapProfile->getProfileEffort ();
         # uniform distribution when no effort is set
         if ( $sumProfileEffort == 0 )
         {
            $tProfileEffortFactor = ( 1 / $profileCount );
         }
         else
         {
            $tProfileEffortFactor = round ( ( $tProfileEffort / $sumProfileEffort ), 2 );
         }
         # bug data
         $doneBugIds = $this->getDoneBugIds ();
         $tDoneBugAmount = count ( $doneBugIds );
         if ( $useEta )
         {
            # calculate eta for profile
            $fullEta = ( $this->getFullEta () ) * $profileCount;
            $doneEta = 0;
            foreach ( $doneBugIds as $doneBugId )
            {
               $doneEta += $this->getSingleEta ( $doneBugId );
            }
            $doneEtaPercent = ( ( $doneEta / ( $fullEta ) ) * 100 );
            $doneEtaPercent = $doneEtaPercent * $profileCount * $tProfileEffortFactor;
            $wholeProgress += $doneEtaPercent;
            $profileHash = $tProfileId . ';' . $doneEtaPercent;
         }
         else
         {
            $tVersionProgress = ( $tDoneBugAmount / $allBugCount );
            $progessDonePercent = ( $tVersionProgress * 100 * $tProfileEffortFactor );
            $wholeProgress += $progessDonePercent;
            $profileHash = $tProfileId . ';' . $progessDonePercent;
         }

         array_push ( $this->profileHashArray, $profileHash );
         $this->resetDoneBugIds ();
         $this->setProfileId ( -1 );
      }

      $this->progressPercent = $wholeProgress;
   }
}