<?php
require_once ( __DIR__ . '/../core/roadmap_constant_api.php' );
require_once ( __DIR__ . '/../core/rProApi.php' );
require_once ( __DIR__ . '/rProfileManager.php' );
require_once ( __DIR__ . '/rProfile.php' );
require_once ( __DIR__ . '/rGroupManager.php' );
require_once ( __DIR__ . '/rGroup.php' );

/**
 * Class roadmap_html_api
 *
 * provides functions to print html content
 *
 * @author Stefan Schwarz
 */
class rHtmlApi
{
   /**
    * Prints a radio button element
    *
    * @param $colspan
    * @param $name
    */
   public static function htmlPluginConfigRadio ( $name, $colspan = 1 )
   {
      echo '<td width="100px" colspan="' . $colspan . '">';
      echo '<label>';
      echo '<input type="radio" name="' . $name . '" value="1"';
      echo ( ON == plugin_config_get ( $name ) ) ? 'checked="checked"' : '';
      echo '/>' . lang_get ( 'yes' );
      echo '</label>';
      echo '<label>';
      echo '<input type="radio" name="' . $name . '" value="0"';
      echo ( OFF == plugin_config_get ( $name ) ) ? 'checked="checked"' : '';
      echo '/>' . lang_get ( 'no' );
      echo '</label>';
      echo '</td>';
   }

   /**
    * Prints a category column in the plugin config area
    *
    * @param $class
    * @param $colspan
    * @param $lang_string
    * @return string
    */
   public static function htmlPluginConfigOutputCol ( $class, $lang_string, $colspan = 1 )
   {
      echo '<td class="' . $class . '" colspan="' . $colspan . '">' . plugin_lang_get ( $lang_string ) . '</td>';
   }

   /**
    * prints opening table tag
    *
    * @param null $id
    */
   public static function htmlPluginConfigOpenTable ( $id = null )
   {
      $htmlString = '<table align="center" cellspacing="1" class="config-table"';
      if ( $id != null )
      {
         $htmlString .= ' id="' . $id . '"';
      }
      $htmlString .= '>';
      echo $htmlString;
   }

   /**
    * print progress bar for single roadmap
    *
    * @param $progress
    * @param $progressString
    * @param $versionId
    */
   private static function printSingleProgressbar ( $progress, $progressString, $versionId )
   {
      echo '<div class="progress9001">';
      $progressHtmlString = '<span class="bar single" style="width: ' . $progress . '%; white-space: nowrap;">' . $progressString . '</span>';
      echo $progressHtmlString;
      echo '</div>';

      self::htmlPluginAddDirectoryProgressBar ( $versionId, $progressHtmlString );
   }

   /**
    * print progress bar for a progress group
    *
    * @param roadmap $roadmap
    */
   private static function printScaledProgressbar ( roadmap $roadmap )
   {
      $useEta = $roadmap->getEtaIsSet ();
      $profileHashMap = $roadmap->getProfileHashArray ();
      $fullEta = $roadmap->getFullEta ();
      $doneEta = 0;
      echo '<div class="progress9001">';
      if ( empty( $profileHashMap ) == false )
      {
         $profileHashCount = count ( $profileHashMap );
         $sumProgressHtmlString = '';
         for ( $index = 0; $index < $profileHashCount; $index++ )
         {
            # extract profile data
            $profileHash = explode ( ';', $profileHashMap[ $index ] );
            $hashProfileId = $profileHash[ 0 ];
            $hashProgress = $profileHash[ 1 ];

            # get profile color
            $profile = new rProfile( $hashProfileId );
            $profileColor = '#' . $profile->getProfileColor ();

            $tempEta = round ( ( ( $hashProgress / 100 ) * $fullEta ), 1 );

            $progressHtmlString = '';

            # first bar
            if ( $index == 0 )
            {
               $progressHtmlString .= '<div class="bar left" style="width: ' . $hashProgress . '%; background: ' . $profileColor . ';">';
               $progressHtmlString .= rProApi::getRoadmapProgress ( $useEta, $tempEta, $hashProgress );
               $progressHtmlString .= '</div><!--';
            }
            # n - 2 (first, last) following
            elseif ( $index == ( $profileHashCount - 1 ) )
            {
               $progressHtmlString .= '--><div class="bar right" style="width: ' . $hashProgress . '%; background: ' . $profileColor . ';">';
               $progressHtmlString .= rProApi::getRoadmapProgress ( $useEta, $tempEta, $hashProgress );
               $progressHtmlString .= '</div>';
            }
            # last bar
            else
            {
               $progressHtmlString .= '--><div class="bar middle" style="width: ' . $hashProgress . '%; background: ' . $profileColor . ';">';
               $progressHtmlString .= rProApi::getRoadmapProgress ( $useEta, $tempEta, $hashProgress );
               $progressHtmlString .= '</div><!--';
            }
            echo $progressHtmlString;

            $sumProgressHtmlString .= $progressHtmlString;
            $doneEta += $tempEta;
         }
         $versionId = $roadmap->getVersionId ();
         self::htmlPluginAddDirectoryProgressBar ( $versionId, $sumProgressHtmlString );
      }

      echo '</div>';
      echo '<div class="progress-suffix">';
      if ( $useEta == true )
      {
         $calculatedDoneEta = rProApi::calculateEtaUnit ( $doneEta );
         $calculatedFullEta = rProApi::calculateEtaUnit ( $fullEta );
         echo '&nbsp;(' . $calculatedDoneEta[ 0 ] . '&nbsp;' . $calculatedFullEta[ 1 ] . '&nbsp;' . lang_get ( 'from' ) . '&nbsp;' . $calculatedFullEta[ 0 ] . '&nbsp;' . $calculatedFullEta[ 1 ];
      }
      else
      {
         $bugCount = count ( $roadmap->getBugIds () );
         echo '&nbsp;(' . round ( $roadmap->getGroupProgressPercent (), 1 ) . '%&nbsp;' . lang_get ( 'from' ) . '&nbsp;' . $bugCount . '&nbsp;' . lang_get ( 'issues' );
      }
      echo ')';
      echo '</div>';
   }

   /**
    * print progress as text
    *
    * @param roadmap $roadmap
    */
   public static function printVersionProgressAsText ( roadmap $roadmap )
   {
      $overallBugAmount = count ( $roadmap->getBugIds () );
      $doneBugAmount = count ( $roadmap->getDoneBugIds () );
      $progressPercent = $roadmap->getSingleProgressPercent ();
      $useEta = $roadmap->getEtaIsSet ();
      echo '<div class="tr"><div class="td">' . PHP_EOL;
      if ( $useEta && config_get ( 'enable_eta' ) )
      {
         echo sprintf ( plugin_lang_get ( 'roadmap_page_resolved_time' ), $doneBugAmount, $overallBugAmount );
      }
      else
      {
         echo sprintf ( lang_get ( 'resolved_progress' ), $doneBugAmount, $overallBugAmount, $progressPercent );
      }
      echo '</div></div>' . PHP_EOL;
   }

   /**
    * wraps content into a cell
    *
    * @param $content
    */
   public static function printWrapperInHTML ( $content )
   {
      echo '<div class="tr"><div class="td">' . $content . '</div></div>' . PHP_EOL;
   }

   /**
    * print the initial html content for the directory
    */
   public static function htmlPluginDirectory ()
   {
      echo '<div class="table" id="directory">';
      echo '<span class="pagetitle">' . plugin_lang_get ( 'roadmap_page_directory' ) . '</span>';
      echo '</div>';
      self::htmlPluginSpacer ();
   }

   /**
    * print the plugin profile switcher
    */
   public static function printProfileSwitcher ()
   {
      $groupIds = rGroupManager::getRGroupIds ();
      $groupCount = count ( $groupIds );

      $profileIds = rProfileManager::getRProfileIds ();
      $profileCount = count ( $profileIds );

      echo '<div class="table_center"><div class="tr">' . PHP_EOL;
      # groups are available
      if ( $groupCount > 0 )
      {
         foreach ( $groupIds as $groupId )
         {
            $group = new rGroup( $groupId );
            $groupName = $group->getGroupName ();

            echo '<div class="td">';
            self::htmlLinkGroupSwitcher ( $groupName, $groupId );
            echo '</div>' . PHP_EOL;
         }
      }
      # no groups available
      else
      {
         # print roadmap_profile-links
         if ( $profileCount > 0 )
         {
            foreach ( $profileIds as $profileId )
            {
               $profile = new rProfile( $profileId );
               $profileName = $profile->getProfileName ();

               echo '<div class="td">';
               self::htmlLinkProfileSwitcher ( string_display ( $profileName ), $profileId );
               echo '</div>' . PHP_EOL;
            }
         }
         # show whole progress, when there is more then one different profile
         if ( $profileCount > 1 )
         {
            echo '<div class="td">';
            self::htmlLinkProfileSwitcher ( plugin_lang_get ( 'roadmap_page_whole_progress' ) );
            echo '</div>' . PHP_EOL;
         }
      }
      echo '</div></div>' . PHP_EOL;
   }

   /**
    * print profile switcher for a group
    *
    * @param $groupId
    */
   public static function htmlGroupProfileSwitcher ( $groupId )
   {
      $group = new rGroup( $groupId );
      $groupProfileIds = explode ( ';', $group->getGroupProfiles () );
      $groupProfileIdCount = count ( $groupProfileIds );

      echo '<div class="table_center"><div class="tr">' . PHP_EOL;
      if ( $groupProfileIdCount > 0 )
      {
         foreach ( $groupProfileIds as $groupProfileId )
         {
            $profile = new rProfile( $groupProfileId );
            $profileName = $profile->getProfileName ();
            echo '<div class="td">';
            self::htmlLinkGroupProfileSwitcher ( string_display ( $profileName ), $groupId, $groupProfileId );
            echo '</div>' . PHP_EOL;
         }
      }
      # show whole progress, when there is more then one different profile
      if ( $groupProfileIdCount > 1 )
      {
         echo '<div class="td">';
         self::htmlLinkGroupProfileSwitcher ( plugin_lang_get ( 'roadmap_page_whole_progress' ), $groupId );
         echo '</div>' . PHP_EOL;
      }
      echo '</div></div>' . PHP_EOL;
   }

   /**
    * print the links for a group
    *
    * @param $groupName
    * @param $groupId
    */
   private static function htmlLinkGroupSwitcher ( $groupName, $groupId )
   {
      $getVersionId = $_GET[ 'version_id' ];
      $getProjectId = $_GET[ 'project_id' ];
      $currentProjectId = helper_get_current_project ();

      echo '[ <a href="' . plugin_page ( 'roadmap_page' ) . '&amp;group_id=';
      # check specific profile id is given
      if ( $groupId != null )
      {
         echo $groupId;
      }
      # check version id is get parameter
      if ( $getVersionId != null )
      {
         echo '&amp;version_id=' . $getVersionId;
      }
      # check project id is get parameter
      if ( $getProjectId != null )
      {
         echo '&amp;project_id=' . $getProjectId;
      }
      echo '&amp;sproject_id=' . $currentProjectId;
      echo '">';
      echo $groupName;
      echo '</a> ]';
   }

   /**
    * print the link for a profile
    *
    * @param $linkDescription
    * @param null $profileId
    */
   private static function htmlLinkProfileSwitcher ( $linkDescription, $profileId = null )
   {
      $getVersionId = $_GET[ 'version_id' ];
      $getProjectId = $_GET[ 'project_id' ];
      $currentProjectId = helper_get_current_project ();

      echo '[ <a href="' . plugin_page ( 'roadmap_page' ) . '&amp;profile_id=';
      # check specific profile id is given
      if ( $profileId != null )
      {
         echo $profileId;
      }
      else
      {
         echo '-1';
      }
      # check version id is get parameter
      if ( $getVersionId != null )
      {
         echo '&amp;version_id=' . $getVersionId;
      }
      # check project id is get parameter
      if ( $getProjectId != null )
      {
         echo '&amp;project_id=' . $getProjectId;
      }
      echo '&amp;sproject_id=' . $currentProjectId;
      echo '">';
      echo $linkDescription;
      echo '</a> ]';
   }

   /**
    * print profile link in a group
    *
    * @param $linkDescription
    * @param $groupId
    * @param null $profileId
    */
   private static function htmlLinkGroupProfileSwitcher ( $linkDescription, $groupId, $profileId = null )
   {
      $getVersionId = $_GET[ 'version_id' ];
      $getProjectId = $_GET[ 'project_id' ];
      $currentProjectId = helper_get_current_project ();

      echo '[ <a href="' . plugin_page ( 'roadmap_page' ) . '&amp;group_id=' . $groupId . '&amp;profile_id=';
      # check specific profile id is given
      if ( $profileId != null )
      {
         echo $profileId;
      }
      else
      {
         echo '-1';
      }
      # check version id is get parameter
      if ( $getVersionId != null )
      {
         echo '&amp;version_id=' . $getVersionId;
      }
      # check project id is get parameter
      if ( $getProjectId != null )
      {
         echo '&amp;project_id=' . $getProjectId;
      }
      echo '&amp;sproject_id=' . $currentProjectId;
      echo '">';
      echo $linkDescription;
      echo '</a> ]';
   }

   /**
    * print the progress of a roadmap
    *
    * @param roadmap $roadmap
    */
   public static function printVersionProgress ( roadmap $roadmap )
   {
      echo '<div class="tr"><div class="td">';
      $profileId = $roadmap->getProfileId ();
      if ( $profileId == -1 )
      {
         self::printScaledProgressbar ( $roadmap );
      }
      else
      {
         $useEta = $roadmap->getEtaIsSet ();
         $doneEta = $roadmap->getDoneEta ();
         $fullEta = $roadmap->getFullEta ();
         $versionId = $roadmap->getVersionId ();
         $progressPercent = $roadmap->getSingleProgressPercent ();
         if ( $useEta && config_get ( 'enable_eta' ) )
         {
            $calculatedDoneEta = rProApi::calculateEtaUnit ( $doneEta );
            $calculatedFullEta = rProApi::calculateEtaUnit ( $fullEta );
            $progressString = $calculatedDoneEta[ 0 ] . '&nbsp;' . $calculatedDoneEta[ 1 ] .
               '&nbsp;' . lang_get ( 'from' ) . '&nbsp;' . $calculatedFullEta[ 0 ] . '&nbsp;' . $calculatedFullEta[ 1 ];
            self::printSingleProgressbar ( $progressPercent, $progressString, $versionId );
         }
         else
         {
            $bugIds = $roadmap->getBugIds ();
            $bugCount = count ( $bugIds );
            $progressString = $progressPercent . '%&nbsp;' . lang_get ( 'from' ) . '&nbsp;' . $bugCount . '&nbsp;' . lang_get ( 'issues' );
            self::printSingleProgressbar ( $progressPercent, $progressString, $versionId );
         }
      }
      echo '</div></div>' . PHP_EOL;
   }

   /**
    * print the bug list of a roadmap
    *
    * @param $bugIds
    * @param bool $doneBugs
    */
   public static function printBugList ( $bugIds, $doneBugs = false )
   {
      foreach ( $bugIds as $bugId )
      {
         $bug = bug_get ( $bugId );
         $userId = $bug->handler_id;
         echo '<div class="tr">';
         # line through, if bug is done
         self::htmlPluginBugCol ( $doneBugs );
         # bug id
         echo string_get_bug_view_link ( $bugId ) . '&nbsp;';
         # bug category
         echo string_display_line ( category_full_name ( $bug->category_id ) );
         # bug symbols
         rProApi::calcBugSmybols ( $bugId );
         # bug summary
         echo string_display_line ( $bug->summary );
         # bug assigned user
         if ( $userId > 0 )
         {
            echo '&nbsp;(<a href="' . config_get ( 'path' ) . '/view_user_page.php?id=' . $userId . '">' .
               user_get_name ( $userId ) . '</a>' . ')';
         }
         # bug status
         echo '&nbsp;-&nbsp;' . string_display_line ( get_enum_element ( 'status', $bug->status ) ) . '.';
         echo '</div></div>' . PHP_EOL;
      }
   }

   /**
    * print opening cell element for a bug
    *
    * @param $bugIsDone
    */
   public static function htmlPluginBugCol ( $bugIsDone )
   {
      if ( $bugIsDone )
      {
         echo '<div class="td done">';
      }
      else
      {
         echo '<div class="td">';
      }
   }

   /**
    * print the project title in a roadmap
    *
    * @param $profileId
    * @param $projectId
    */
   public static function htmlPluginProjectTitle ( $profileId, $projectId )
   {
      $profile = new rProfile( $profileId );
      $profileName = string_display ( $profile->getProfileName () );
      $projectName = string_display ( project_get_name ( $projectId ) );

      echo '<div class="tr"><div class="td"><span class="pagetitle" id="p' . $projectId . '">';
      if ( $profileId == -1 )
      {
         echo sprintf ( plugin_lang_get ( 'roadmap_page_version_title' ), $projectName, plugin_lang_get ( 'roadmap_page_whole_progress' ) );
      }
      else
      {
         echo sprintf ( plugin_lang_get ( 'roadmap_page_version_title' ), $projectName, $profileName );
      }
      echo '</span></div></div>';
   }

   /**
    * print spacer element
    */
   public static function htmlPluginSpacer ()
   {
      echo '<div class="tr"><div class="td"><div class="spacer"></div></div></div>';
   }

   /**
    * triggers whiteboard menu if installed
    */
   public static function htmlPluginTriggerWhiteboardMenu ()
   {
      if ( plugin_is_installed ( 'WhiteboardMenu' ) &&
         file_exists ( config_get_global ( 'plugin_path' ) . 'WhiteboardMenu' )
      )
      {
         require_once __DIR__ . '/../../WhiteboardMenu/core/whiteboard_print_api.php';
         whiteboard_print_api::printWhiteboardMenu ();
      }
   }

   /**
    * add version entry to directory
    *
    * @param $projectId
    * @param $versionId
    * @param $versionName
    */
   public static function htmlPluginAddDirectoryVersionEntry ( $projectId, $versionId, $versionName )
   {
      echo '<script type="text/javascript">';
      echo 'addVersionEntryToDirectory (\'' . project_get_name ( $projectId ) . '\',\'' . $versionId . '\',\'' . $versionName . '\');';
      echo '</script>';
   }

   /**
    * add project entry to directory
    *
    * @param $projectId
    */
   public static function htmlPluginAddDirectoryProjectEntry ( $projectId )
   {
      $projectName = project_get_name ( $projectId );
      echo '<script type="text/javascript">';
      echo 'addProjectEntryToDirectory (\'directory\',\'' . $projectId . '\',\'' . $projectName . '\');';
      echo '</script>';
   }

   /**
    * add progress bar to directory
    *
    * @param $versionId
    * @param $htmlString
    */
   public static function htmlPluginAddDirectoryProgressBar ( $versionId, $htmlString )
   {
      echo '<script type="text/javascript">';
      echo 'addProgressBarToDirectory (\'' . $versionId . '\',\'' . $htmlString . '\');';
      echo '</script>';
   }

   /**
    * prints initial ressources for the page
    *
    * @param $profileColor
    */
   public static function htmlInitializeRessources ( $profileColor )
   {
      echo '<link rel="stylesheet" href="' . ROADMAPPRO_PLUGIN_URL . 'files/roadmappro.css.php?profile_color=' . $profileColor . '"/>';
      echo '<script type="text/javascript" src="' . ROADMAPPRO_PLUGIN_URL . 'files/roadmappro.js"></script>';
      echo '<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>';
      echo '<script type="text/javascript">backToTop();</script>';
   }
}