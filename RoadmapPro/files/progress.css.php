<?php
  $background = "#" . $_GET[ 'profile_color' ];
  header('Content-type: text/css; charset: UTF-8');
?>

.progress9000 {
    position: relative;
    background: #fff;
    background-color: #fff;
    width: 400px;
    border: 1px solid #d7d7d7;
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-radius: 6px;
    margin-top: 1em;
    margin-bottom: 1em;
    padding: 1px 4px 1px 1px;
}

.progress9000 .bar {
    padding: 0px 0px 0px 1px;
    display: block;
    position: relative;
    background: <?php echo $background ?>;
    background-color: <?php echo $background ?>;
    background-image: -webkit-linear-gradient(top, <?php echo $background ?>,<?php echo $background ?>);
    background-image: -moz-linear-gradient(top, <?php echo $background ?>, <?php echo $background ?>);
    background-image: -ms-linear-gradient(top, <?php echo $background ?>, <?php echo $background ?>);
    background-image: -o-linear-gradient(top, <?php echo $background ?>, <?php echo $background ?>);
    background-image: linear-gradient(top, <?php echo $background ?>, <?php echo $background ?>);
    text-align: center;
    font-weight: normal;
    height: 1.4em;
    line-height: 1.4em;
    color: #111;
    border: solid 1px <?php echo $background ?>;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
    border-radius: 4px;
}

pre {
    display: inline-block;
    white-space: pre-wrap; /* CSS 3 */
    white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
    white-space: -pre-wrap; /* Opera 4-6 */
    white-space: -o-pre-wrap; /* Opera 7 */
    word-wrap: break-word; /* Internet Explorer 5.5+ */
}

div.rcv_tooltip_title {
    border-bottom: 1px solid #777;
    text-align: left;
}

div.rcv_tooltip_content {
    margin-top: 4px;
    text-align: left;
}

.rcv_tooltip {
    outline: none;
}

.rcv_tooltip strong {
    line-height: 30px;
}

.rcv_tooltip:hover {
    text-decoration: none;
}

.rcv_tooltip span {
    z-index: 10;
    display: none;
    padding: 4px 8px;
    margin-top: 30px;
    margin-left: -200px;
    width: 250px;
    line-height: 16px;
}

.rcv_tooltip:hover span {
    display: inline;
    position: absolute;
    border: 1px solid #777;
    color: #121212;
    background-color: #dedede;
    background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#fdfdfd), to(#dedede));
    background-image: -webkit-linear-gradient(top, #fdfdfd, #dedede);
    background-image: -moz-linear-gradient(top, #fdfdfd, #dedede);
    background-image: -ms-linear-gradient(top, #fdfdfd, #dedede);
    background-image: -o-linear-gradient(top, #fdfdfd, #dedede);
    background-image: linear-gradient(top, #fdfdfd, #dedede);
}

/* CSS3 */
.rcv_tooltip span {
    border: solid 1px #777;
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-radius: 6px;
}

div.table {
    display: table;
    border-collapse: collapse;
}

div.table_center {
    margin-left: auto;
    margin-right: auto;
    display: table;
    border-collapse: collapse;
}

div.tr {
    display: table-row;
}

div.td {
    display: table-cell;
    border: none;
    padding: 5px;
}

div.td20 {
    display: table-cell;
    border: none;
    padding: 5px;
    margin: 20px;
}