<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html>
<head>
   <title>Forecast Search Homepage</title>
<style>


.output
{
    border-style: solid;
    padding: 50px 20px;
    width:350px;
    text-align: "center";
}
form.one {
    border-style: solid;
    padding: 10px 20px;
    width:300px;
    text-align: left;
}

.submit1
{
margin-left: 115px;
}

.text1
{
margin-left: 3px;
margin-top: -100px;

}
.text2
{
margin-left: 70px;
margin-top: -100px;
}
.text3
{
margin-left: 65px;
}
.text4
{
margin-left: 50px;
}
.link
{
margin-left:80px;
vertical-align: ="center";
}
</style>
</head>
<body>

<?php
$status="";
$stateo = "";
  if((isset($_GET["submit"])))
  {
  if($_GET['phpControl']=="true")
  {

     $streetAddress=($_GET["street"]);
     $streetAddress=strtr($streetAddress, " ", "+");
     $city=($_GET["city"]);
     $city=strtr($city, " ", "+");
     $stateo=($_GET["state"]);
     $state=strtr($stateo, " ", "+");
     
     $startIndex = strpos($state,"(") +1;
     $state = substr($state,$startIndex,2);
      
      $unit = $_GET["degree"];

     $APIID = "AIzaSyC5YBTuVVHvPkvbzx9RHq1D3w_NqKRBtds";

     $address = 'address'.'='.$streetAddress.','.$city.','.$state.'&key='.$APIID;
     $url='https://maps.googleapis.com/maps/api/geocode/xml?'."$address";
     
     $xmlDoc = SimpleXML_load_file($url);
     $status= $xmlDoc->status;

     if ($status=="OK") 
     {
     $lat = $xmlDoc->result->geometry[0]->location->lat;
     $lng = $xmlDoc->result->geometry[0]->location->lng;
     
    
    $forecastAPI = "5df610e715fc97a29f2133ec7c405931";
    $forecastAddress = $forecastAPI.'/'.$lat.','.$lng.'?units='.$unit.'&exclude=flags';
    $forecastURL = "https://api.forecast.io/forecast/".$forecastAddress;

    $jsonString = file_get_contents($forecastURL);
    $jsonString = utf8_encode($jsonString); 
    $json = json_decode($jsonString,true);
    
           $current=$json['currently'];

           $precipIntensity = $current['precipIntensity'];

            if($precipIntensity>=0 && $precipIntensity<0.002)
              $precipIntensity="None";
            else if($precipIntensity>=0.002 && $precipIntensity<0.017)
              $precipIntensity="Very Light";
            else if($precipIntensity>=0.0017 && $precipIntensity<0.1)
              $precipIntensity="Light";
            else if($precipIntensity>=0.1 && $precipIntensity<0.4)
              $precipIntensity="Moderate";
            else if($precipIntensity>=0.4)
              $precipIntensity="Heavy";

            $degree=$unit;
            if($degree=="si")
            {
              $degree= "&deg"." C";
              $windSpeedUnit = "mps";
              $visibilityUnit = "km";
            } 
            else if($degree=="us")
            {
              $degree= "&deg"." F";
              $windSpeedUnit = "mph";
              $visibilityUnit = "mi";
            }
           $icon = $current['icon'];
           $precipProbability=$current['precipProbability'];
           $precipProbability=$precipProbability*100;
           $windSpeed=round($current['windSpeed']);
           $dewPoint=round($current['dewPoint']);
           $visibility=round($current['visibility']);
           $summary=$current['summary'];
           $temperature=round($current['temperature']);
           $humidity=$current['humidity'];
           $humidity=$humidity*100;
           
           $daily=$json['daily']; 

           
           $data=$daily['data'];
           
           $time=$data[0];
           
           $timezone=$json['timezone'];
          
           date_default_timezone_set($timezone); 
           $sunriseTime = date('h:i A', $time['sunriseTime']);
          
           date_default_timezone_set($timezone);
           $sunsetTime = date('h:i A', $time['sunsetTime']);
            
          if($icon=="clear-day")
            $img = "http://cs-server.usc.edu:45678/hw/hw6/images/clear.png";
          else if($icon=="storm")
            $img = "http://cs-server.usc.edu:45678/hw/hw6/images/Storm.png";
          else if($icon=="clear-night")
            $img = "http://cs-server.usc.edu:45678/hw/hw6/images/clear_night.png";
          else if($icon=="rain")
            $img = "http://cs-server.usc.edu:45678/hw/hw6/images/rain.png";
          else if($icon=="snow")
            $img = "http://cs-server.usc.edu:45678/hw/hw6/images/snow.png";
          else if($icon=="sleet")
            $img = "http://cs-server.usc.edu:45678/hw/hw6/images/sleet.png";
          else if($icon=="wind")
            $img = "http://cs-server.usc.edu:45678/hw/hw6/images/wind.png";
          else if($icon=="fog")
            $img = "http://cs-server.usc.edu:45678/hw/hw6/images/fog.png";
          else if($icon=="cloudy")
            $img = "http://cs-server.usc.edu:45678/hw/hw6/images/cloudy.png";
          else if($icon=="partly-cloudy-day")
            $img = "http://cs-server.usc.edu:45678/hw/hw6/images/cloud_day.png";
          else if($icon=="partly-cloudy-night")
            $img = "http://cs-server.usc.edu:45678/hw/hw6/images/cloud_night.png";

            
           $table= '<br/><br/><center><table class="output" text-align="center">';
           $table.='<tr><th colspan="2">'.$summary.'</tr></th>';
           $table.= '<tr><th colspan="2">'.$temperature.$degree.'</tr></th>';
           $table.='<tr><th colspan="2"><img src='.$img.' title='.$icon.' alt='.$summary.'></tr></th>';
           $table.='<tr><td>Precipitation:</td><td>'.$precipIntensity.'</td></tr>';
           $table.='<tr><td>Chance of Rain:</td><td>'.$precipProbability.'%</td></tr>';
           $table.='<tr><td>Wind Speed:</td><td>'.$windSpeed.' '.$windSpeedUnit.'</td></tr>';
           $table.='<tr><td>Dew Point:</td><td>'.$dewPoint.'</td></tr>';
           $table.='<tr><td>Humidity:</td><td>'.$humidity.'%</td></tr>';
           $table.='<tr><td>Visibility:</td><td>'.$visibility.' '.$visibilityUnit.'</td></tr>';
           $table.='<tr><td>Sunrise:</td><td>'.$sunriseTime.'</td></tr>';
           $table.='<tr><td>Sunset:</td><td>'.$sunsetTime.'</td></tr>';

           $table.='</table></center>';
           //echo $table;
      }
      else 
      {
        echo '<script language="javascript">';  
        echo 'alert("Wrong address entered")';
        echo "</script>";
      }
  }
  }
?>

<script type="text/javascript">

function validate(form)
{

  if(form.street.value.trim() == "")
   {
       alert("Please Enter the value for Street!");
        form.street.focus();
       return false;
   }

  else if(form.city.value.trim() == "")
   {
       alert("Please Enter the value for City!");
       form.city.focus();
       return false;
   }

  else if(form.state.value == "Select your state")
   {
       alert("Please Choose the value for State!");
       form.state.focus();
       return false;
   }
   else
    {
    form.phpControl.value="true";
    }
}

function clearForm(form)
{
  form.street.value="";
  form.city.value="";
  document.getElementById('state').selectedIndex=0;
  document.getElementById("Fahrenheit").checked = "true";
  document.getElementById("tabid").style.display = "none";
}
</script>
<div>
<center><h2>Forecast Search</h2></center>
<center><form method="GET" action="" class="one" name="form"></br></br>



      Street Address:* <input class="text1" type="text" name="street" size=25 value="<?php if(isset($_GET['submit'])){echo $_GET['street'];}?>"/></br></br>
      City:*           <input class="text2" type="text" name="city" size=25 value="<?php if(isset($_GET['submit'])){echo $_GET['city'];}?>"/></br></br>
      State:*          <Select class="text3" id="state" name="state">
                       <option selected=selected value="Select your state" size=10>Select your state</option>
                       <option value="Alabama(AL)" <?php if($stateo == "Alabama(AL)") echo "selected"; ?> >Alabama
                       <option value="Alaska(AK)" <?php if($stateo == "Alaska(AK)") echo "selected"; ?> >Alaska
                      <option value="Arizona(AZ)" <?php if($stateo == "Arizona(AZ)") echo "selected"; ?>>Arizona
<option value="Arkansas(AR)" <?php if($stateo == "Arkansas(AR)") echo "selected"; ?>>Arkansas
<option value="California(CA)" <?php if($stateo == "California(CA)") echo "selected"; ?>>California
<option value="Colorado(CO)" <?php if($stateo == "Colorado(CO)") echo "selected"; ?> >Colorado
<option value="Connecticut(CT)" <?php if($stateo == "Connecticut(CT)") echo "selected"; ?>>Connecticut

<option value="Delaware(DE)" <?php if($stateo == "Delaware(DE)") echo "selected"; ?> >Delaware

<option value="District Of Columbia(DC)" <?php if($stateo == "District Of Columbia(DC)") echo "selected"; ?> >District Of Columbia

<option value="Florida(FL)" <?php if($stateo == "Florida(FL)") echo "selected"; ?> >Florida

<option value="Georgia(GA)" <?php if($stateo == "Georgia(GA)") echo "selected"; ?> >Georgia

<option value="Hawaii(HI)" <?php if($stateo == "Hawaii(HI)") echo "selected"; ?> >Hawaii

<option value="Idaho(ID)" <?php if($stateo == "Idaho(ID)") echo "selected"; ?> >Idaho
<option value="Illinois(IL)" <?php if($stateo == "Illinois(IL)") echo "selected"; ?> >Illinois

<option value="Indiana(IN)" <?php if($stateo == "Indiana(IN)") echo "selected"; ?> >Indiana

<option value="Iowa(IA)" <?php if($stateo == "Iowa(IA)") echo "selected"; ?> >Iowa
<option value="Kansas(KS)" <?php if($stateo == "Kansas(KS)") echo "selected"; ?> >Kansas

<option value="Kentucky(KY)" <?php if($stateo == "Kentucky(KY)") echo "selected"; ?> >Kentucky
<option value="Louisiana(LA)" <?php if($stateo == "Louisiana(LA)") echo "selected"; ?> >Louisiana

<option value="Maine(ME)" <?php if($stateo == "Maine(ME)") echo "selected"; ?> >Maine
<option value="Maryland(MD)" <?php if($stateo == "Maryland(MD)") echo "selected"; ?> >Maryland

<option value="Massachusetts(MA)" <?php if($stateo == "Massachusetts(MA)") echo "selected"; ?> >Massachusetts
<option value="Michigan(MI)" <?php if($stateo == "Michigan(MI)") echo "selected"; ?> >Michigan

<option value="Minnesota(MN)" <?php if($stateo == "Minnesota(MN)") echo "selected"; ?>>Minnesota
<option value="Mississippi(MS)" <?php if($stateo == "Mississippi(MS)") echo "selected"; ?> >Mississippi

<option value="Missouri(MO)" <?php if($stateo == "Missouri(MO)") echo "selected"; ?> >Missouri
<option value="Montana(MT)" <?php if($stateo == "Montana(MT)") echo "selected"; ?> >Montana
<option value="Nebraska(NE)" <?php if($stateo == "Nebraska(NE)") echo "selected"; ?> >Nebraska
<option value="Nevada(NV)" <?php if($stateo == "Nevada(NV)") echo "selected"; ?> >Nevada
<option value="New Hampshire(NH)" <?php if($stateo == "New Hampshire(NH)") echo "selected"; ?> >New Hampshire

<option value="New Jersey(NJ)" <?php if($stateo == "New Jersey(NJ)") echo "selected"; ?> >New Jersey

<option value="New Mexico(NM)" <?php if($stateo == "New Mexico(NM)") echo "selected"; ?> >New Mexico

<option value="New York(NY)" <?php if($stateo == "New York(NY)") echo "selected"; ?> >New York
<option value="North Carolina(NC)" <?php if($stateo == "North Carolina(NC)") echo "selected"; ?> >North Carolina
<option value="North Dakota(ND)" <?php if($stateo == "North Dakota(ND)") echo "selected"; ?> >North Dakota

<option value="Ohio(OH)" <?php if($stateo == "Ohio(OH)") echo "selected"; ?> >Ohio
<option value="Oklahoma(OK)" <?php if($stateo == "Oklahoma(OK)") echo "selected"; ?> >Oklahoma

<option value="Oregon(OR)" <?php if($stateo == "Oregon(OR)") echo "selected"; ?> >Oregon

<option value="Pennsylvania(PA)" <?php if($stateo == "Pennsylvania(PA)") echo "selected"; ?> >Pennsylvania
<option value="Rhode Island(RI)" <?php if($stateo == "Rhode Island(RI)") echo "selected"; ?> >Rhode Island

<option value="South Carolina(SC)" <?php if($stateo == "South Carolina(SC)") echo "selected"; ?> >South Carolina

<option value="South Dakota(SD)" <?php if($stateo == "South Dakota(SD)") echo "selected"; ?> >South Dakota(SD)
<option value="Tennessee(TN)" <?php if($stateo == "Tennessee(TN)") echo "selected"; ?> >Tennessee

<option value="Texas(TX)" <?php if($stateo == "Texas(TX)") echo "selected"; ?> >Texas

<option value="Utah(UT)" <?php if($stateo == "Utah(UT)") echo "selected"; ?> >Utah
<option value="Vermont(VT)" <?php if($stateo == "Vermont(VT)") echo "selected"; ?> >Vermont
<option value="Virginia(VA)" <?php if($stateo == "Virginia(VA)") echo "selected"; ?> >Virginia

<option value="Washington(WA)" <?php if($stateo == "Washington(WA)") echo "selected"; ?> >Washington

<option value="West Virginia(WV)" <?php if($stateo == "West Virginia(WV)") echo "selected"; ?> >West Virginia
<option value="Wisconsin(WI)" <?php if($stateo == "Wisconsin(WI)") echo "selected"; ?> >Wisconsin
<option value="Wyoming(WY)" <?php if($stateo == "Wyoming(WY)") echo "selected"; ?> >Wyoming

                       </Select></br></br>
      Degree:*         <input class="text4" type="radio" name="degree" <?php if(isset($_GET["submit"])){if($unit == "us") echo "checked";} else echo "checked"; ?> value="us" id="Fahrenheit">Fahrenheit
                       <input type="radio" name="degree" value="si" <?php if(isset($_GET["submit"])){if($unit == "si") echo "checked";} ?> id="Celsius">Celsius</br></br>
                       <input class="submit1" type="submit" name="submit" value="Search" onClick="return validate(this.form);"/>
                       <input type="button" name="reset" value="Clear" onClick="clearForm(this.form);"/>

       <p><I>* - Mandatory fields.</I></p>
      <a href="http://forsecast.io/" class="link">Powered By Forecast.io</a>
      <input type="hidden" name="phpControl" value="FALSE">
     </form></center>
   </div>
<div id="tabid" name= "tab">
<?php 
if($status=="OK")
{
  if(isset($_GET["submit"]))
    echo $table;
}
?>
</div>
</body>
</html>