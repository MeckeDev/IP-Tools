  <!DOCTYPE html>
  <html lang="de" dir="ltr">
    <head>

      <meta charset="utf-8">
      <link rel="stylesheet" href="external/main.css">
      <link rel="icon" type="image/png" href="/external/images/fav.png">

      <title>IPv4 Solution</title>

    </head>
    <body>


  <?

  /* If no Form was filled before */

      if (!isset($_POST['calc']) && !isset($_POST['show']))
      {

  ?>

  <!-- Anleitung zur Nutzung der Seite -->

    <div class="Intro">

      <h1> Introduction </h1>
      <p> Fill the Field and get to the next Field by pressing Enter </p>
      <p> If the last Field is filled, Enter will bring you to the next Page. </p>

    </div>



  <! first Form to get the Amount of Locations and Routers >

      <div class="form-amount">

        <form class="getAmount" autocomplete="off" action="index.eng.php" method="post">
          <input class="inputs" pattern="[0-9]{1,3}" autofocus type="text" required name="location" placeholder="0">
          <label class="noselect">How many Subnets ?</label>
          <br>
          <input class="inputs" pattern="[0-9]{1,3}" type="text" required name="router" placeholder="0">
          <label class="noselect">How many Cities ?</label>
          <button type="submit" name="calc">Continue</button>
        </form>

      </div>

  <?
      }

  /* If the first Form was filled but not the second one */

      elseif (isset($_POST['calc']) && !isset($_POST['show']))
      {

  /* Convert Data to Variables */

        $x = 0;
        $num = $_POST['location'] + $_POST['router'];
        $router = $_POST['router'];
        $location = $_POST['location'];


        ?>

  <! second Form to get the Net-ID >

        <form class="getPCs" action="index.eng.php" method="post">
          <input class="inputs" autofocus
          pattern="^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])"
          type="text" required name="netID" placeholder="192.168.0.0">

          <label class="iplabel noselect">Net-ID</label>
          <br>
        <?

  /*  second Form to get Data for each Location */

        while ($x < $location)
        {
          $x++;
          ?>
          <input class="inputs" pattern="[0-9]{1,5}" type="text" required name="<? echo $x;?>" placeholder="0">

            <label class="location noselect">How many PCs on Location <? echo $x; ?> ?</label>

          <?
        }
        ?>

  <! hidden Form to transfer Data from the first Form to the Calculation >

        <input type="hidden" name="count" value="<? echo $num; ?>">
        <input type="hidden" name="router" value="<? echo $router; ?>">
        <button type="submit" required name="show">Continue</button>
      </form>

      <br>


  <?  }

  /* If all Forms were filled and you are ready for the Results */

        elseif (isset($_POST['show']))
        {

  /* Convert all Data into Variables */

          $x = 0;
          $num = $_POST['count'];
          $router = $_POST['router'];
          $location = $_POST['count'] - $_POST['router'];
          $ip = $_POST['netID'];

  /* generate Array with the Amounts for each Location */

          $arr = [];

          while ($x < $location)
          {

            $x++;
            $loc = $_POST[$x];
            $new = array_push($arr, $loc);

          }

  /* Sort Array to get the biggest Subnets calculated first */

          rsort($arr);

          echo "<div class=\"ID\"> Net-ID = ". $ip ." </div>";

  /* generate Array do turn the Location-Amounts to legit potencies of 2 */

          $arr2 = [];

          $arrNum = [4,8,16,32,64,128,256,512,1024,2048,4096,8192,16384,32768,65536];

          foreach ($arr as $value)
          {
            foreach ($arrNum as $numb)
            {
              $idx = NULL;

              if ($value <= ($numb-3))
              {
                $idx = $numb;
                $new2 = array_push($arr2, $idx);
                break;
              }
            }
          }

  /* add Routers to the Potencies-Array */

          $x = 0;

          while ($x < $router)
          {

            $x++;

            $new2 = array_push($arr2, 4);

          }
  ?>

  <! build the Table of Results >

          <div class="tables">

            <table class="IPtable">
              <th>IPs</th>  <th>NetID</th> <th>first IP</th>  <th>last IP</th>  <th>Broadcast</th>  <th>SM</th> <th>SM Decimal</th>

  <?

  /* Loop through the Potencies Array */

            for($i = 0;$i < count($arr2);$i ++)
            {

  /* Convert Array-Position into $val Variable */

              $val = $arr2[$i];

  ?>

  <!-- enter Potency into the Table -->

              <tr><td><? echo $val; ?></td>


  <! enter NetID to Table >

                <td><? echo $ip; ?></td>

  <?

  /* calculate first IP */

              $newIP = ip2long($ip);

              $newIP += 1;

              $ip = long2ip($newIP);

  ?>

  <! enter first IP into Table >

                <td><? echo $ip; ?></td>

  <?

  /* calculate last IP */

            $newIP = ip2long($ip);

            $newIP += $val-3;

            $ip = long2ip($newIP);

  ?>

  <! enter last IP into Table >

              <td><? echo $ip; ?></td>

  <?

  /* calculate Broadcast */

          $newIP = ip2long($ip);

          $newIP += 1;

          $ip = long2ip($newIP);

  ?>

  <! enter Broadcast into Table >

            <td><? echo $ip; ?></td>

  <?

  /* calculate Subnetmask and enter it into the Table */

            if ($val == 4)
            {
              ?>
              <td>30</td> <td>255.255.255.252</td>
              <?
            }
            elseif ($val == 8)
            {
              ?>
              <td>29</td> <td>255.255.255.248</td>
              <?
            }
            elseif ($val == 16)
            {
              ?>
              <td>28</td> <td>255.255.255.240</td>
              <?
            }
            elseif ($val == 32)
            {
              ?>
              <td>27</td> <td>255.255.255.224</td>
              <?
            }
            elseif ($val == 64)
            {
              ?>
              <td>26</td> <td>255.255.255.192</td>
              <?
            }
            elseif ($val == 128)
            {
              ?>
              <td>25</td> <td>255.255.255.128</td>
              <?
            }
            elseif ($val == 256)
            {
              ?>
              <td>24</td> <td>255.255.255.0</td>
              <?
            }
            elseif ($val == 512)
            {
              ?>
              <td>23</td> <td>255.255.254.0</td>
              <?
            }
            elseif ($val == 1024)
            {
              ?>
              <td>22</td> <td>255.255.252.0</td>
              <?
            }
            elseif ($val == 2048)
            {
              ?>
              <td>21</td> <td>255.255.248.0</td>
              <?
            }
            elseif ($val == 4096)
            {
              ?>
              <td>20</td> <td>255.255.240.0</td>
              <?
            }
            elseif ($val == 8192)
            {
              ?>
              <td>19</td> <td>255.255.224.0</td>
              <?
            }
            elseif ($val == 16384)
            {
              ?>
              <td>18</td> <td>255.255.192.0</td>
              <?
            }
            elseif ($val == 32768)
            {
              ?>
              <td>17</td> <td>255.255.128.0</td>
              <?
            }
            elseif ($val == 65536)
            {
              ?>
              <td>16</td> <td>255.255.0.0</td>
              <?
            }

            ?>
            </tr>
            <?

  /* calculate a new Net-ID */

          $newIP = ip2long($ip);

          $newIP += 1;

          $ip = long2ip($newIP);

  ?>

            </tr>

  <?

            }

  ?>

            </table>

          </div>

  <?

  }

  ?>





    </body>

    <script type="text/javascript">

    $('.inputs').keydown(function (e) {
    if (e.which === 13) {
        var index = $('.inputs').index(this) + 1;
        $('.inputs').eq(index).focus();
    }
    });


    </script>

  </html>
