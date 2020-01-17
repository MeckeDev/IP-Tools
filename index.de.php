<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>

    <meta charset="utf-8">
    <link rel="stylesheet" href="external/main.css">
    <link rel="icon" type="image/png" href="/external/images/fav.png">

    <title>IPv4 Lösung</title>

<?

/* Wenn noch kein Formular ausgefüllt wurde */

    if (!isset($_POST['calc']) && !isset($_POST['show']))
    {

?>

<!-- Anleitung zur Nutzung der Seite -->

  <div class="Intro">

    <h1> Anleitung </h1>
    <p> Feld ausfüllen und einfach mit Enter zum nächsten Feld wechseln </p>
    <p> Wenn das letzte Feld erreicht ist führt Enter einen zur nächsten Seite. </p>

  </div>




<! erstes Formular zur Eingabe von Anzahl der Standorte und Anzahl der Router >

    <div class="form-amount">

      <form class="getAmount" autocomplete="off" action="index.de.php" method="post">
        <input class="inputs" pattern="[0-9]{1,3}" autofocus type="text" required name="location" placeholder="0">
        <label class="noselect">Anzahl der Subnetze?</label>
        <br>
        <input class="inputs" pattern="[0-9]{1,3}" type="text" required name="router" placeholder="0">
        <label class="noselect">Anzahl der Städte?</label>
        <button type="submit" name="calc">Weiter</button>
      </form>

    </div>

<?
    }

/* Wenn das erste Formular ausgefüllt wurde aber das zweite noch nicht */

    elseif (isset($_POST['calc']) && !isset($_POST['show']))
    {

/* wandel die aus Formular 1 erhaltenen Daten in Variablen um */

      $x = 0;
      $num = $_POST['location'] + $_POST['router'];
      $router = $_POST['router'];
      $location = $_POST['location'];


      ?>

<! zweites Formular zur Eingabe der Netz-ID >

      <form class="getPCs" action="index.de.php" method="post">
        <input class="inputs" autofocus
        pattern="^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])"
        type="text" required name="netzID" placeholder="192.168.0.0">

        <label class="noselect">Netz-ID</label>
        <br>
      <?

/*  zweites Formular zur Eingabe der Anzahl der PCs pro Standort */

      while ($x < $location)
      {
        $x++;
        ?>

          <input class="inputs" pattern="[0-9]{1,5}" type="text" required name="<? echo $x;?>" placeholder="0">
          <label class="location noselect">Anzahl der PCs im Subnetz <? echo $x; ?></label>

        <?
      }
      ?>

<! Versteckte Variablen aus Formular 1 zur weitergabe an die Berechnung >

      <input type="hidden" name="count" value="<? echo $num; ?>">
      <input type="hidden" name="router" value="<? echo $router; ?>">
      <button type="submit" required name="show">Weiter</button>
    </form>

    <br>


<?  }

/* Wenn alle Formulare ausgefüllt wurden */

      elseif (isset($_POST['show']))
      {

/* wandel alle erhaltenen Daten in Variablen um */

        $x = 0;
        $num = $_POST['count'];
        $router = $_POST['router'];
        $location = $_POST['count'] - $_POST['router'];
        $ip = $_POST['netzID'];

/* generiere Array mit Anzahl der PCs alle Standorte */

        $arr = [];

        while ($x < $location)
        {

          $x++;
          $loc = $_POST[$x];
          $new = array_push($arr, $loc);

        }

/* Sortiere Array damit die größten Netze als erstes berechnet werden */

        rsort($arr);

        echo "<div class=\"ID\"> Netz-ID = ". $ip ." </div>";

/* erstelle Array um die Anzahl er PCs in optimale Netze mit 2er Potenz zuzuordnen */

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

/* füge Router zu Array mit den PCs hinzu */

        $x = 0;

        while ($x < $router)
        {

          $x++;

          $new2 = array_push($arr2, 4);

        }
?>

<! erstelle Tabelle mit Header für das Ergbnis >

        <div class="tables">

          <table class="IPtable">
            <th>IPs</th>  <th>NetzID</th> <th>1. IP</th>  <th>letzte IP</th>  <th>Broadcast</th>  <th>SM</th> <th>SM-Dezimal</th>

<?

/* Schleife um das Array mit den PCs (in 2er Potenzen) durchzugehen */

          for($i = 0;$i < count($arr2);$i ++)
          {

/* wandel Array-Position in Variable $val um */

            $val = $arr2[$i];

?>

<!-- trage 2er Potenz in Tabelle ein -->

            <tr><td><? echo $val; ?></td>


<! trage aktuelle Netz-ID in die Tabelle ein >

              <td><? echo $ip; ?></td>

<?

/* erstelle 1. IP */

            $newIP = ip2long($ip);

            $newIP += 1;

            $ip = long2ip($newIP);

?>

<! trage 1. IP in die Tabelle ein >

              <td><? echo $ip; ?></td>

<?

/* berechne letzte IP */

          $newIP = ip2long($ip);

          $newIP += $val-3;

          $ip = long2ip($newIP);

?>

<! trage letzte IP in die Tabelle ein >

            <td><? echo $ip; ?></td>

<?

/* berechne den Broadcast */

        $newIP = ip2long($ip);

        $newIP += 1;

        $ip = long2ip($newIP);

?>

<! trage den Broadcast in die Tabelle ein >

          <td><? echo $ip; ?></td>

<?

/* berechne die Subnetzmaske und trage sie in die Tabelle ein*/

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

/* berechne die neue Netz-ID */

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
</html>
