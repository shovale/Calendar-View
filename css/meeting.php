
<?php
/**
 * @author Shoval Eliav <shoval123@hotmail.com>
 * @version 1.0.0
 * @api REST outlook.com V2.0
 * This program are send requested to mail server
 * Server returns data
 */
/**
 * *@param $sec         -   Refresh page time in second   
 * *@param $username    -   Credential with domain
 * *@param $password    -   Credential with domain
 * *@param $formatDate  -   Date format
 * *@param $start       -   Start time 
 * *@param $end         -   End time    
 */
$page = $_SERVER['PHP_SELF'];
$sec = "60";
$username='domain\username';
$password='password';
$formatDate = date('Y-m-d');
$Start = ''.$formatDate.'T00:01:00Z'; 
$End = ''.$formatDate.'T23:59:00Z';
global $date;
global $timezone;
global $arr;

/** @param  $calendarName - Name of the calendar to show meetings */
$calendarName = 'calendar address';
/** @param $URL - Sending requsting to mail server */
$URL = 'https://mail.pluristem.com/api/v2.0/Users/'.$calendarName.'/calendarview?startDateTime='.$Start.'&endDateTime='.$End.'&$orderby=Start/DateTime';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$URL);//Saving URL in ch parameter
curl_setopt($ch, CURLOPT_TIMEOUT, 30); //Timeout after 30 seconds
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);//Passing http
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");//Sending username and password of user                  
$result=curl_exec($ch);//Saving the jquery file in result
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);//Get status code
curl_close ($ch);//Closing session
$result = json_decode($result);//Parse json file
$arr=cvf_convert_object_to_array($result);//Converting file to array

/**
 * Foreach in matrix saving 1 parameter
 * @param current date $date
 */
if (is_array($arr) || is_object($arr)){       foreach($arr['value'] as $row => $item)      {   $date = convertDateFormat(($item['Start']['DateTime']));     }     }

/**
 * Foreach in matrix saving 3 parameters:
 * @param Meeting starting time $startTime
 * @param Meeting ending time $endtTime
 * @param Meeting subject $subject
*/ 
if (is_array($arr) || is_object($arr))
{
    foreach($arr['value'] as $row => $item) 
    {
        $startTime = convertTimeFormat($item['Start']['DateTime']);
        $endTime = convertTimeFormat($item['End']['DateTime']);
        $subject = ($item['Subject']);
    }
}
/** @method cvf_convert_object_to_array - are converting object to array */

function cvf_convert_object_to_array($data)
{
    if (is_object($data))           {   $data = get_object_vars($data);         }
    if (is_array($data))            {   return array_map(__FUNCTION__, $data);  }
    else                            {   return $data;                           }
}

/**
 * Function to convert time from UTC to GMT +2
 *
 * @param [type] $timeToConvert to convert
 * @return new convert time
 */
function convertTimeFormat($timeToConvert)
{
    $timeZoneNameFrom="UTC";
    $timeZoneNameTo = "Asia/Jerusalem";
    $newTime =  date_create($timeToConvert, new DateTimeZone($timeZoneNameFrom))
    ->setTimezone(new DateTimeZone($timeZoneNameTo))->format("H:i");
    return $newTime;
}
/**
 * Function to convert date to d-m-Y
 *
 * @param [type] $dateToConvert to convert 
 * @return new convert date
 */
function convertDateFormat($dateToConvert)
{
    $timeZoneNameFrom="UTC";
    $timeZoneNameTo = "Asia/Jerusalem";
    $newDate =  date_create($dateToConvert, new DateTimeZone($timeZoneNameFrom))
    ->setTimezone(new DateTimeZone($timeZoneNameTo))->format("d-m-Y");
    return $newDate;
}
/**
 * @param [type] $timeToConvert - time to convert
 * @return new time after converting ("H:i")
 */
function convertCurrentTime($timeToConvert)
{
    $timeZoneNameFrom="Asia/Jerusalem";
    $timeZoneNameTo = "Asia/Jerusalem";
    $newTime =  date_create($timeToConvert, new DateTimeZone($timeZoneNameFrom))
    ->setTimezone(new DateTimeZone($timeZoneNameTo))->format("H:i");
    return $newTime;
}
?>
<!--=====================================
                HTML
=======================================-->
<!DOCTYPE html>
<html>
    <head>
        <!-- css files -->
        <?php include 'css/css.html';  ?>
        <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
    </head>

    <!-- Body --> 
    <body class="container">
        <div class="page-header">
            <!--Title of page - Display the name of meeting room and current date -->
            <h1>
                <mark class='title_page'>
                    Small meeting room floor 1
                    <small>
                            <?php   echo "<font color='white'>".$date;echo "</font>";?>
                    </small>
                </mark>
            </h1>
        </div>

        <!-- Display the meeting of the current day -->
        <?php 
            if (is_array($arr) || is_object($arr))
            {
                $timezone  = +2;
                $currentTime = gmdate("H:i", time() + 3600*($timezone+date("I")))."<br>"; 
                foreach($arr['value'] as $row => $item) 
                { 
                    /*If there meeting right now we display room is busy in red! */
                    if(($currentTime > $startTime = convertTimeFormat($item['Start']['DateTime'])) && ($currentTime <= $endTime = convertTimeFormat($item['End']['DateTime'])))
                    {     
                        echo "<div>";
                        echo "<ul class='list-group'>";
                        echo "<li class='list-group-item list-group-item-danger'>";
                        echo "<center>";echo "Busy";"</center>";
                        echo "</span>";
                        echo "</li>";
                        echo "</ul>";
                        echo "</div>"."<br>";
                        break;    
                    }
                    /*Else we display room as free */
                    if(($currentTime < $startTime = convertTimeFormat($item['Start']['DateTime']))&&( $currentTime < $endTime = convertTimeFormat($item['End']['DateTime'])))
                    {
                        echo "<div>";
                        echo "<ul class='list-group'>";
                        echo "<li class='list-group-item list-group-item-success'>";
                        echo "<center>";echo "Free";"</center>";
                        echo "</span>";
                        echo "</li>";
                        echo "</ul>";
                        echo "</div>"."<br>";
                        break;
                    }
                }
                foreach($arr['value'] as $row => $item) 
                { 
                /* Show the meeting are exsits only!! now past meetings */
                    if(($currentTime > $startTime = convertTimeFormat($item['Start']['DateTime'])) && ($currentTime <= $endTime = convertTimeFormat($item['End']['DateTime'])))
                    {     
                        echo "<div>";
                        echo "<ul class='list-group'>";
                        echo "<li class='list-group-item list-group-item-danger'>";
                        echo $startTime = convertTimeFormat($item['Start']['DateTime'])."<br>"."<br>";
                        echo "<center>";
                        echo    "<font size='18'>". $subject = ($item['Subject']).'</font>'."<br>"."<br>";
                        echo "</center>";
                        echo    $endTime = convertTimeFormat($item['End']['DateTime']);  
                        echo "<span class='badge'>";
                        echo $owner = ($item['Organizer']['EmailAddress']['Name']);
                        echo "</span>";
                        echo "</li>";
                        echo "</ul>";
                        echo "</div>"."<br>";
                    }
                    elseif(($currentTime < $startTime = convertTimeFormat($item['Start']['DateTime']))&&( $currentTime < $endTime = convertTimeFormat($item['End']['DateTime'])))
                    {
                        echo "<div>";
                        echo "<ul class='list-group'>";
                        echo "<li class='list-group-item list-group-item-info'>";
                        echo $startTime = convertTimeFormat($item['Start']['DateTime'])."<br>"."<br>";
                        echo "<center>";
                        echo    "<font size='18'>". $subject = ($item['Subject']).'</font>'."<br>"."<br>";
                        echo "</center>";
                        echo    $endTime = convertTimeFormat($item['End']['DateTime']);  
                        echo "<span class='badge'>";
                        echo $owner = ($item['Organizer']['EmailAddress']['Name']);
                        echo "</span>";
                        echo "</li>";
                        echo "</ul>";
                        echo "</div>"."<br>";
                    } 
                } 
            }
            /*Else if there is a problem to retrive data from mail server - Display spiner loader */
            else
            {
                echo "<br>"."<br>"."<br>";
                echo "<center>";
                echo "<div class='loader'>";
                echo "</div>";
                echo "</center>";
            }          
        ?> 
    </body>
</html>

