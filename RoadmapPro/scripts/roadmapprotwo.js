/**
 * @type {string}
 */
var profileId = "profiles";
/*
 * @type {string}
 */
var thresholdId = "thresholds";
/*
 * @type {string}
 */
var groupId = "groups";
/**
 * @type {number}
 */
var newThresholdCounter = 0;
/**
 * @type {number}
 */
var newProfileCounter = 0;
/**
 * @type {number}
 */
var newGroupCounter = 0;

/**
 * adds an empty threshold row to the config page
 */
$(document).ready(function () {
    /**
     * add threshold row to config menu
     */
    $('#addthresholdrownew').click(function () {
        var divArea = document.getElementById(thresholdId);
        // widget body
        var widgetBodyDiv = document.createElement("div");
        widgetBodyDiv.className = "widget-body";
        widgetBodyDiv.id = "" + newThresholdCounter + "";
        // widget main
        var widgetMainDiv = document.createElement("div");
        widgetMainDiv.className = "widget-main no-padding";
        // table responsive
        var tableResponsiveDiv = document.createElement("div");
        tableResponsiveDiv.className = "table-responsive";
        // table element
        var tableTable = document.createElement("table");
        tableTable.className = "table table-bordered table-condensed table-striped";
        // column 1
        var td1 = document.createElement("td");
        td1.className = "width-25";
        td1.style.width = "25%";
        // column 2
        var td2 = document.createElement("td");
        td2.className = "width-25";
        td2.style.width = "25%";
        // column 3
        var td3 = document.createElement("td");
        td3.className = "width-25";
        td3.style.width = "25%";
        // column 4
        var td4 = document.createElement("td");
        td4.className = "width-25";
        td4.style.width = "25%";

        /** to */
        td1.innerHTML = '<input type="number" step="0.1" name="threshold-to[]" size="15" maxlength="128" value="">';
        /** unit */
        td2.innerHTML = '<input type="text" name="new-threshold-unit-' + newThresholdCounter + '" size="15" maxlength="128" value="">';
        newThresholdCounter++;
        /** factor */
        td3.innerHTML = '<input type="number" step="0.1" name="threshold-factor[]" size="15" maxlength="128" value="">';

        tableTable.appendChild(td1);
        tableTable.appendChild(td2);
        tableTable.appendChild(td3);
        tableTable.appendChild(td4);

        tableResponsiveDiv.appendChild(tableTable);
        widgetMainDiv.appendChild(tableResponsiveDiv);
        widgetBodyDiv.appendChild(widgetMainDiv);
        divArea.appendChild(widgetBodyDiv);
    });

    /**
     * delete threshold row from config menu
     */
    $('#delthresholdrownew').click(function () {
        if (newThresholdCounter > 0) {
            $('#' + thresholdId).children().last().remove();
            newThresholdCounter--;
        }
    });

    /**
     * add profile row to config menu
     */
    $('a[data-state_profileid]').click(function () {
        var divArea = document.getElementById(profileId);
        // widget body
        var widgetBodyDiv = document.createElement("div");
        widgetBodyDiv.className = "widget-body";
        widgetBodyDiv.id = "" + newProfileCounter + "";
        // widget main
        var widgetMainDiv = document.createElement("div");
        widgetMainDiv.className = "widget-main no-padding";
        // table responsive
        var tableResponsiveDiv = document.createElement("div");
        tableResponsiveDiv.className = "table-responsive";
        // table element
        var tableTable = document.createElement("table");
        tableTable.className = "table table-bordered table-condensed table-striped";
        // column 1
        var td1 = document.createElement("td");
        td1.className = "width-20";
        td1.style.width = "20%";
        // column 2
        var td2 = document.createElement("td");
        td2.className = "width-20";
        td2.style.width = "20%";
        // column 3
        var td3 = document.createElement("td");
        td3.className = "width-15";
        td3.style.width = "15%";
        // column 4
        var td4 = document.createElement("td");
        td4.className = "width-15";
        td4.style.width = "15%";
        // column 5
        var td5 = document.createElement("td");
        td4.className = "width-15";
        td4.style.width = "15%";
        // column 6
        var td6 = document.createElement("td");
        td4.className = "width-15";
        td4.style.width = "15%";

        var $this = $(this);
        var statusValues = $this.data('state_profileid');
        var statusStrings = $this.data('state_profilename');
        var optionstring = '';
        //alert("statusstrings: " + statusStrings);
        for (var i = 0; i < statusValues.length; i++) {
            var value = statusValues[i];
            var string = statusStrings[i];

            optionstring += '<option value="' + value + '">' + string + '</option>'
        }

        /** name */
        td1.innerHTML = '<input type="text" name="profile-name[]" size="15" maxlength="128" value="">';
        /** status */
        td2.innerHTML = '<select name="new-status-' + newProfileCounter + '[]" multiple="multiple">' + optionstring + '</select>';
        /** color */
        td3.innerHTML = '<label><input class="color {pickerFace:4,pickerClosable:true}" type="text" name="profile-color[]" value=""/></label>';
        /** priority */
        td4.innerHTML = '<input type="number" name="profile-prio[]" size="15" maxlength="3" value="">';
        /** effort */
        td5.innerHTML = '<input type="number" name="profile-effort[]" size="15" maxlength="3" value="">';
        /** action */
        td6.innerHTML = '';

        tableTable.appendChild(td1);
        tableTable.appendChild(td2);
        tableTable.appendChild(td3);
        tableTable.appendChild(td4);
        tableTable.appendChild(td5);
        tableTable.appendChild(td6);

        tableResponsiveDiv.appendChild(tableTable);
        widgetMainDiv.appendChild(tableResponsiveDiv);
        widgetBodyDiv.appendChild(widgetMainDiv);
        divArea.appendChild(widgetBodyDiv);

        newProfileCounter++;
    });

    /**
     * delete profile row from config menu
     */
    $('#delprofilerownew').click(function () {
        if (newProfileCounter > 0) {
            $('#' + profileId).children().last().remove();
            newProfileCounter--;
        }
    });

    /**
     * add group row to config menu
     */
    $('a[data-state_groupid]').click(function () {
        var divArea = document.getElementById(groupId);
        // widget body
        var widgetBodyDiv = document.createElement("div");
        widgetBodyDiv.className = "widget-body";
        widgetBodyDiv.id = "" + newGroupCounter + "";
        // widget main
        var widgetMainDiv = document.createElement("div");
        widgetMainDiv.className = "widget-main no-padding";
        // table responsive
        var tableResponsiveDiv = document.createElement("div");
        tableResponsiveDiv.className = "table-responsive";
        // table element
        var tableTable = document.createElement("table");
        tableTable.className = "table table-bordered table-condensed table-striped";
        // column 1
        var td1 = document.createElement("td");
        td1.className = "width-40";
        td1.style.width = "40%";
        // column 2
        var td2 = document.createElement("td");
        td2.className = "width-30";
        td2.style.width = "30%";
        // column 3
        var td3 = document.createElement("td");
        td3.className = "width-30";
        td3.style.width = "30%";

        var $this = $(this);
        var profileIds = $this.data('state_groupid');
        var profileNames = $this.data('state_groupname');
        var optionstring = '';
        for (var i = 0; i < profileIds.length; i++) {
            var value = profileIds[i];
            var string = profileNames[i];

            optionstring += '<option value="' + value + '">' + string + '</option>'
        }

        /** name */
        td1.innerHTML = '<input type="text" name="group-name[]" size="15" maxlength="128" value="">';
        /** profiles */
        td2.innerHTML = '<select name="new-group-profile-' + newGroupCounter + '[]" multiple="multiple">' + optionstring + '</select>';
        /** action */
        td3.innerHTML = '';

        tableTable.appendChild(td1);
        tableTable.appendChild(td2);
        tableTable.appendChild(td3);

        tableResponsiveDiv.appendChild(tableTable);
        widgetMainDiv.appendChild(tableResponsiveDiv);
        widgetBodyDiv.appendChild(widgetMainDiv);
        divArea.appendChild(widgetBodyDiv);

        newGroupCounter++;
    });

    /**
     * delete group row from config menu
     */
    $('#delgrouprownew').click(function () {
        if (newGroupCounter > 0) {
            $('#' + groupId).children().last().remove();
            newGroupCounter--;
        }
    });
});