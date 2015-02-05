<?php

class product{
    protected $name               = "";
    protected $ice_cream_flavours = array();
    protected $liters_per_scoop   = 1;
    protected $discount_percent   = 0;
    public  $config               = array();
    protected $db_link;
    protected $profit_percent     = 5;
    public    $total_price        = 0;


    public function set_config() {
        $config = include('config.php');
        $error_code = true;
        if(!$config){
            echo "config.php not found".PHP_EOL;
            $error_code = false;
        }else{
            foreach ($config as $key => $value){
                if($value == ""){
                    echo "Please set $key in config.php".PHP_EOL;
                    $error_code = false;
                }else{
                    $this->config[$key] = $value;
                }
            }
        }
        $this->db_link = mysqli_connect($this->config['db_host'], $this->config['db_user'], $this->config['db_pass'], $this->config['db_name']);
        if (!$this->db_link) {
            $this->printx('Could not connect: ' . mysqli_error($this->db_link));
        }
        return $error_code;
    }

    public function execute_query($query){
        if($this->db_link){
            $result = mysqli_query($this->db_link, $query);
            if(!$result){
                $this->printx(mysqli_error($this->db_link));
                return false;
            }
            return $result;
        }
    }

    protected function printx($data){
        if($this->config['debug'] == 'true'){
            echo $data."\n";
        }
    }

    protected function check_stock($item_type, $item_name, $demand){
        $stock = mysqli_fetch_row($this->execute_query("SELECT quantity_available FROM $item_type WHERE name='$item_name'"));
        if($stock && $stock[0] >= $demand){
           return true;
        }else{
           return false;
        }
    }

    protected function calculate_discount($item_name){
        $discount = mysqli_fetch_row($this->execute_query("SELECT discount_percent FROM products WHERE name='$item_name'"));
        return $discount[0];
    }
}


class ice_cream_cone extends product{
    protected $no_of_scoops   = 1;
    protected $serving_vessel = "";

    public function __construct($ice_cream_flavours, $serving_vessel, $no_of_scoops = 1){
        $this->name = "Ice cream cone";
        $this->ice_cream_flavours = $ice_cream_flavours;
        $this->no_of_scoops = $no_of_scoops;
        $this->serving_vessel = $serving_vessel;
        $this->discount_percent   = 0;
        $this->set_config();
    }

    private function check_availability(){
        if(count($this->ice_cream_flavours) == 1){
            if(!$this->check_stock('ice_cream_flavours',$this->ice_cream_flavours[0], $this->no_of_scoops)){
                return false;
            }
        }
        for ($i = 0; $i < $this->no_of_scoops; $i++){
            if(!$this->check_stock('ice_cream_flavours', $this->ice_cream_flavours[$i], 1)){
                return false;
            }
        }
        if(!$this->check_stock('serving_vessels', $this->serving_vessel, 1)){
            return false;
        }
        return true;
    }

    private function update_stock(){
        if(1 == count($this->ice_cream_flavours)){
            $tot_ice_cream_flavour = $this->liters_per_scoop * $this->no_of_scoops;
            $flavour = $this->ice_cream_flavours[0];
            $query = "UPDATE ice_cream_flavours SET quantity_available =  quantity_available - $tot_ice_cream_flavour WHERE name='$flavour'";
            $this->execute_query($query);
        }else{
            foreach($this->ice_cream_flavours as $ice_cream_flavour){
                $tot_ice_cream_flavour = $this->liters_per_scoop;
                $query = "UPDATE ice_cream_flavours SET quantity_available =  quantity_available - $tot_ice_cream_flavour WHERE name='$ice_cream_flavour'";
                $this->execute_query($query);
            }
        }
        $query = "UPDATE serving_vessels SET quantity_available =  quantity_available - 1 WHERE name='$this->serving_vessel'";
        $this->execute_query($query);
    }

    private function calculate_price(){
        $tot_price = 0;
        if(1 == count($this->ice_cream_flavours)){
            $flavour = $this->ice_cream_flavours[0];
            $query = "SELECT price_per_litre FROM ice_cream_flavours WHERE name='$flavour'";
            $price_per_litre = mysqli_fetch_row($this->execute_query($query));
            $tot_price = $price_per_litre[0] * $this->no_of_scoops * $this->liters_per_scoop;
        }else{
            foreach($this->ice_cream_flavours as $ice_cream_flavour){
                $query = "SELECT price_per_litre FROM ice_cream_flavours WHERE name='$ice_cream_flavour'";
                $price_per_litre = mysqli_fetch_row($this->execute_query($query));
                $tot_price += ($price_per_litre[0] * $this->liters_per_scoop);
            }
        }
        $serving_vessel = $this->serving_vessel;
        $serving_vessel_price = mysqli_fetch_row($this->execute_query("SELECT price_per_item FROM serving_vessels WHERE name='$serving_vessel'"));
        $profit = ($tot_price + $serving_vessel_price[0])*($this->profit_percent/100);
        $this->total_price = $tot_price + $serving_vessel_price[0] + $profit;
        $this->discount_percent = $this->calculate_discount($this->name);
        return ($this->total_price - ($this->total_price * $this->discount_percent/100));
    }

    public function prepare(){
        if(empty($this->ice_cream_flavours)){
            echo "Error..Please select at least one flavour<br>";
        }else if(!((2 == $this->no_of_scoops) || (1 == $this->no_of_scoops))){
            echo "Error..No of scoops should be one or two<br>";
        }
        if($this->check_availability()){
            $this->update_stock();
            $total_price = round($this->calculate_price(), 2);
            echo $this->name." ";
            for ($i = 0; $i < count($this->ice_cream_flavours); $i++){
                echo $this->ice_cream_flavours[$i];
                if($i != count($this->ice_cream_flavours)-1){
                    echo " & ";
                }
            }
            echo "<br> Price = $total_price<br>";
            $this->printx("Availability OK");
            return $total_price;
        }else{
            echo "Sorry..Not enough stock available for $this->name<br>";
            $this->printx("Not enough stock available");
        }
        return false;
    }
}

class milk_shake extends product{
    protected $milk_type = "";

    public function __construct($milk_type, $ice_cream_flavour){
        $this->name = "Milk shake";
        $this->ice_cream_flavours = array($ice_cream_flavour);
        $this->milk_type = $milk_type;
        $this->discount_percent   = 0;
        $this->set_config();
    }

    public function prepare(){
        if(empty($this->ice_cream_flavours)){
            echo "Error..Please select at least one flavour<br>";
        }else if(!(1 == count($this->ice_cream_flavours))){
            echo "Error..Only one ice cream flavour can be selected<br>";
        }
        if($this->check_availability()){
            $this->update_stock();
            $total_price = round($this->calculate_price(), 2);
            echo $this->name." ";
            echo $this->ice_cream_flavours[0]." ";
            echo $this->milk_type." ";
            echo "<br> Price = $total_price ";
            if($this->discount_percent != 0){
                echo "(".$this->discount_percent."% discount)";
            }
            echo "<br>";
            $this->printx("Availability OK");
            return $total_price;
        }else{
            echo "Sorry..Not enough stock available for $this->name<br>";
            $this->printx("Not enough stock available");
        }
        return false;
    }

    private function check_availability(){
        if(!$this->check_stock('ice_cream_flavours',$this->ice_cream_flavours[0], 1)){
            return false;
        }
        if(!$this->check_stock('milk', $this->milk_type, 1)){
            return false;
        }
        return true;
    }

    private function update_stock(){
        $tot_ice_cream_flavour = $this->liters_per_scoop;
        $flavour = $this->ice_cream_flavours[0];
        $query = "UPDATE ice_cream_flavours SET quantity_available =  quantity_available - $tot_ice_cream_flavour WHERE name='$flavour'";
        $this->execute_query($query);

        $query = "UPDATE milk SET quantity_available =  quantity_available - 1 WHERE name='$this->milk_type'";
        $this->execute_query($query);
    }

    private function calculate_price(){
        $flavour = $this->ice_cream_flavours[0];
        $query = "SELECT price_per_litre FROM ice_cream_flavours WHERE name='$flavour'";
        $price_per_litre = mysqli_fetch_row($this->execute_query($query));
        $ice_cream_price = $price_per_litre[0];

        $milk_type = $this->milk_type;
        $milk_price = mysqli_fetch_row($this->execute_query("SELECT price_per_litre FROM milk WHERE name='$milk_type'"));
        $profit = ($ice_cream_price + $milk_price[0])*($this->profit_percent/100);
        $this->total_price = $ice_cream_price + $milk_price[0] + $profit;
        $this->discount_percent = $this->calculate_discount($this->name);
        return ($this->total_price - ($this->total_price * $this->discount_percent/100));
    }
}

class float extends product{
    protected $no_of_scoops   = 1;
    protected $soda = "";

    public function __construct($ice_cream_flavours, $soda, $no_of_scoops = 1){
        $this->name = "Float";
        $this->ice_cream_flavours = $ice_cream_flavours;
        $this->soda = $soda;
        $this->no_of_scoops = $no_of_scoops;
        $this->discount_percent   = 0;
        $this->set_config();
    }

    public function prepare(){
        if(empty($this->ice_cream_flavours)){
            echo "Error..Please select at least one flavour<br>";
        }
        if($this->check_availability()){
            $this->update_stock();
            $total_price = round($this->calculate_price(), 2);
            echo $this->name." ";
            foreach ($this->ice_cream_flavours as $flavour => $count){
                echo $flavour."*".$count;
            }

            echo " and ".$this->soda." ";
            echo "<br> Price = $total_price ";
            if($this->discount_percent != 0){
                echo "(".$this->discount_percent."% discount)";
            }
            echo "<br>";
            $this->printx("Availability OK");
            return $total_price;
        }else{
            echo "Sorry..Not enough stock available for $this->name<br>";
            $this->printx("Not enough stock available");
        }
        return false;
    }

    private function check_availability(){
        $this->ice_cream_flavours = array_count_values($this->ice_cream_flavours);
        foreach ($this->ice_cream_flavours as $flavour => $count){
            if(!$this->check_stock('ice_cream_flavours', $flavour, $count)){
                return false;
            }
        }
        if(!$this->check_stock('sodas', $this->soda, 1)){
            return false;
        }
        return true;
    }

    private function update_stock(){
        foreach ($this->ice_cream_flavours as $flavour => $count){
            $tot_ice_cream_flavour = $this->liters_per_scoop*$count;
            $query = "UPDATE ice_cream_flavours SET quantity_available =  quantity_available - $tot_ice_cream_flavour WHERE name='$flavour'";
            $this->execute_query($query);
        }
        $query = "UPDATE sodas SET quantity_available =  quantity_available - 1 WHERE name='$this->soda'";
        $this->execute_query($query);
    }

    private function calculate_price(){
        $ice_cream_price = 0;
        foreach ($this->ice_cream_flavours as $flavour => $count){
            $query = "SELECT price_per_litre FROM ice_cream_flavours WHERE name='$flavour'";
            $price_per_litre = mysqli_fetch_row($this->execute_query($query));
            $ice_cream_price += $price_per_litre[0];
        }

        $soda = $this->soda;
        $soda_price = mysqli_fetch_row($this->execute_query("SELECT price_per_litre FROM sodas WHERE name='$soda'"));
        $profit = ($ice_cream_price + $soda_price[0])*($this->profit_percent/100);
        $this->total_price = $ice_cream_price + $soda_price[0] + $profit;
        $this->discount_percent = $this->calculate_discount($this->name);
        return ($this->total_price - ($this->total_price * $this->discount_percent/100));
    }
}

$product = new product();
$grand_total = 0;

$ice_cream_cone1 = new ice_cream_cone(array('Vanilla', 'Chocolate'), 'Cup', 2);
$ice_cream_cone2 = new ice_cream_cone(array('Strawberry', 'Grapes'), 'Waffle cone', 2);
$ice_cream_cone3 = new ice_cream_cone(array('Mango'), 'Cup', 1);

$grand_total += $ice_cream_cone1->prepare();
$grand_total += $ice_cream_cone2->prepare();
$grand_total += $ice_cream_cone3->prepare();

$milk_shake = new milk_shake('skim', 'Chocolate');
$grand_total += $milk_shake->prepare();

$float1 = new float(array('Vanilla', 'Chocolate', 'Mango'), 'Orange crush', 3);
$float2 = new float(array('Strawberry', 'Grapes'), 'Cherry 7UP', 2);
$float3 = new float(array('Mango', 'Grapes'), 'Mountain Dew', 2);

$grand_total += $float1->prepare();
$grand_total += $float2->prepare();
$grand_total += $float3->prepare();
if($grand_total !== false){
    echo "<br>Grand Total = $grand_total<br>";
}