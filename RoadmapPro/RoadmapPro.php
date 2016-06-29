<?php

class RoadmapProPlugin extends MantisPlugin
{
    function register ()
    {
        $this->name = 'Roadmap Pro';
        $this->description = 'Extended Roadmap with additional progress information';
        $this->page = 'config_page';

        $this->version = '1.0.0';
        $this->requires = array
        (
            'MantisCore' => '1.2.0, <= 1.3.99'
        );

        $this->author = 'Stefan Schwarz';
        $this->contact = '';
        $this->url = 'https://github.com/Cre-ator';
    }

    function hooks ()
    {
        $hooks = array
        (
            'EVENT_LAYOUT_PAGE_FOOTER' => 'footer',
            'EVENT_MENU_MAIN' => 'menu'
        );
        return $hooks;
    }

    function config ()
    {
        return array
        (
            'show_menu' => ON,
            'show_footer' => ON,
            'roadmap_pro_access_level' => ADMINISTRATOR
        );
    }

    function schema ()
    {
        return array
        (
            array
            (
                'CreateTableSQL', array ( plugin_table ( 'profile' ), "
            id              I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
            profile_name    C(250)  DEFAULT '',
            profile_status  C(250)  DEFAULT ''
            " )
            )
        );
    }

    function get_user_has_level ()
    {
        $project_id = helper_get_current_project ();
        $user_id = auth_get_current_user_id ();

        return user_get_access_level ( $user_id, $project_id ) >= plugin_config_get ( 'roadmap_pro_access_level', ADMINISTRATOR );
    }

    function footer ()
    {
        if ( plugin_config_get ( 'show_footer' ) && $this->get_user_has_level () )
        {
            return '<address>' . $this->name . '&nbsp;' . $this->version . '&nbsp;Copyright&nbsp;&copy;&nbsp;2016&nbsp;by&nbsp;' . $this->author . '</address>';
        }
        return null;
    }

    function menu ()
    {
        if ( plugin_config_get ( 'show_menu' ) && $this->get_user_has_level () )
        {
            return '<a href="' . plugin_page ( 'roadmap_page' ) . '">' . plugin_lang_get ( 'menu_title' ) . '</a >';
        }
        return null;
    }
}