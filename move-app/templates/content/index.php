<div>
<h4>From...</h4>
<form action="submit" method="get">
    <h4>Region</h4>
    <select name="fromregion">
        <option selected="selected">Choose one</option>
        <?php
		$regions = array("Vancouver","Island","Fraser","Interior","North");
        
        foreach($regions as $item){
            echo "<option value='$item'>$item</option>";
        }
        ?>
    </select>
    <select name="fromplace">
        <option selected="selected">Choose one</option>
        <?php
        $Island = array("Campbell River","Gold River","Tahsis","Colwood","Western Communities","Courtenay","Duncan","Ganges","Nanaimo","Port Alberni","Ucluelet","Port Hardy","Powell River","Sidney","Tofino","Victoria");
		$Vancouver = array("Vancouver","222 Main","Bella Bella","Bella Coola","Klemtu","Judicial Justice Centre","Downtown Community Court","North Vancouver","Pemberton","Richmond","Robson Square","Sechelt");
		$Fraser = array("Abbotsford","Chilliwack","New Westminster","Port Coquitlam","Surrey");
		$Interior = array("Cranbrook","Creston","Fernie","Golden","Invermere","Sparwood","Kamloops","Chase","Clearwater","Lillooet","Merritt","Kelowna","Nelson","Castlegar","Grand Forks","Nakusp","Rossland","Penticton","Princeton","Salmon Arm","Revelstoke","Vernon");
		$North = array("Dawson Creek","Chetwynd","Tumbler Ridge","Fort Nelson","Fort St. John","Good Hope Lake","Hudson's Hope","Lower Post","Prince George","Fort St. James","Fraser Lake","McBride","McKenzie","Mackenzie","Valemont","Prince Rupert","Masset","Queen Charlotte City","Quesnel","Smithers","Atlin","Burns Lake","Hazelton","Houston","Terrace","Dease Lake","Kitimat","Kwadacha","Tsay Keh Dene","New Aiyansh","Stewart","Vanderhoof","Williams Lake","100 Mile House","Alexis Creek","Anahim Lake","Valemount");
		$months = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
        
        // foreach($Island as $item){
        //     echo "<option value='$item'>$item</option>";
        // }
        foreach($Vancouver as $item){
            echo "<option value='$item'>$item</option>";
        }
        // foreach($Fraser as $item){
        //     echo "<option value='$item'>$item</option>";
        // }
        // foreach($Interior as $item){
        //     echo "<option value='$item'>$item</option>";
        // }
        // foreach($North as $item){
        //     echo "<option value='$item'>$item</option>";
        // }
        ?>
    </select>
    <h4>Court</h4>
    <select name="fromcourt">
        <!-- <option selected="selected">Choose one</option> -->
        <?php
		$courts = array("Court");
        
        foreach($courts as $item){
            echo "<option value='$item'>$item</option>";
        }
        ?>
    </select>
    <h4>Room</h4>
    <select name="fromroom">
        <!-- <option selected="selected">Choose one</option> -->
        <?php
		$rooms = array("Room");
        
        foreach($rooms as $item){
            echo "<option value='$item'>$item</option>";
        }
        ?>
    </select>
    <h4>Month</h4>
    <select name="frommonth">
        <option selected="selected">Choose one</option>
        <?php
		$months = array("01-Jan","02-Feb","03-Mar","04-Apr","05-May","06-Jun","07-Jul","08-Aug","09-Sep","10-Oct","11-Nov","12-Dec");
        
        foreach($months as $item){
            echo "<option value='$item'>$item</option>";
        }
        ?>
    </select>
    <h4>Day</h4>
    <select name="fromday">
        <option selected="selected">Choose one</option>
        <?php
            for ($day = 1; $day<=31; $day++) {
                echo "<option value='$day'>$day</option>";
                }
                   
        ?>
    </select>
    <h4>Person</h4>
    <select name="person">
        <!-- <option selected="selected">Choose one</option> -->
        <?php
		$people = array("Jones, Bob");
        
        foreach($people as $item){
            echo "<option value='$item'>$item</option>";
        }
        ?>
    </select>
<br/>
    </div>
    <div>
<h4>...To</h4>
<h4>Region</h4>
    <select name="toregion">
        <option selected="selected">Choose one</option>
        <?php
		$regions = array("Vancouver","Island","Fraser","Interior","North");
        
        foreach($regions as $item){
            echo "<option value='$item'>$item</option>";
        }
        ?>
    </select>
    <select name="toplace">
        <option selected="selected">Choose one</option>
        <?php
        $Island = array("Campbell River","Gold River","Tahsis","Colwood","Western Communities","Courtenay","Duncan","Ganges","Nanaimo","Port Alberni","Ucluelet","Port Hardy","Powell River","Sidney","Tofino","Victoria");
		$Vancouver = array("Vancouver","222 Main","Bella Bella","Bella Coola","Klemtu","Judicial Justice Centre","Downtown Community Court","North Vancouver","Pemberton","Richmond","Robson Square","Sechelt");
		$Fraser = array("Abbotsford","Chilliwack","New Westminster","Port Coquitlam","Surrey");
		$Interior = array("Cranbrook","Creston","Fernie","Golden","Invermere","Sparwood","Kamloops","Chase","Clearwater","Lillooet","Merritt","Kelowna","Nelson","Castlegar","Grand Forks","Nakusp","Rossland","Penticton","Princeton","Salmon Arm","Revelstoke","Vernon");
		$North = array("Dawson Creek","Chetwynd","Tumbler Ridge","Fort Nelson","Fort St. John","Good Hope Lake","Hudson's Hope","Lower Post","Prince George","Fort St. James","Fraser Lake","McBride","McKenzie","Mackenzie","Valemont","Prince Rupert","Masset","Queen Charlotte City","Quesnel","Smithers","Atlin","Burns Lake","Hazelton","Houston","Terrace","Dease Lake","Kitimat","Kwadacha","Tsay Keh Dene","New Aiyansh","Stewart","Vanderhoof","Williams Lake","100 Mile House","Alexis Creek","Anahim Lake","Valemount");
		// $months = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
        
        // foreach($Island as $item){
        //     echo "<option value='$item'>$item</option>";
        // }
        foreach($Vancouver as $item){
            echo "<option value='$item'>$item</option>";
        }
        // foreach($Fraser as $item){
        //     echo "<option value='$item'>$item</option>";
        // }
        // foreach($Interior as $item){
        //     echo "<option value='$item'>$item</option>";
        // }
        // foreach($North as $item){
        //     echo "<option value='$item'>$item</option>";
        // }
        ?>
    </select>
    <h4>Court</h4>
    <select name="tocourt">
        <!-- <option selected="selected">Choose one</option> -->
        <?php
		$courts = array("Court");
        
        foreach($courts as $item){
            echo "<option value='$item'>$item</option>";
        }
        ?>
    </select>
    <h4>Room</h4>
    <select name="toroom">
        <!-- <option selected="selected">Choose one</option> -->
        <?php
		$rooms = array("Room");
        
        foreach($rooms as $item){
            echo "<option value='$item'>$item</option>";
        }
        ?>
    </select>
    <h4>Month</h4>
    <select name="tomonth">
        <option selected="selected">Choose one</option>
        <?php
		$months = array("01-Jan","02-Feb","03-Mar","04-Apr","05-May","06-Jun","07-Jul","08-Aug","09-Sep","10-Oct","11-Nov","12-Dec");
        
        foreach($months as $item){
            echo "<option value='$item'>$item</option>";
        }
        ?>
    </select>
    <h4>Day</h4>
    <select name="today">
        <option selected="selected">Choose one</option>
        <?php
            for ($day = 1; $day<=31; $day++) {
                echo "<option value='$day'>$day</option>";
                }
                   
        ?>
    </select>
    <input type="submit" name="submit" value=Submit>
</form>
            
</div>
<!-- <a href="/nextcloud/apps/move/Folders">Create Folders</a> -->