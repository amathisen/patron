<?php

require_once("include/define.php");

class seatingChart {
    
    //Array holding the individual seats
    private $seats = array();
    //Maximum number of seats that can be purchased in 1 request
    private $maxRequest;
    //Record of reservation request results
    private $reservation_results = array();

    public function __construct($inputValues) {
        
        //Initialize number of rows, seats per row, and maximum ticket purchase size based on form values, or define.php values if no form values are present
        $rows = (isset($inputValues['initialRows']) ? $inputValues['initialRows'] : DEFAULTROWS);
        $cols = (isset($inputValues['initialSeats']) ? $inputValues['initialSeats'] : DEFAULTSEATS);
        $this->maxRequest = (isset($inputValues['initialMaxTickets']) ? $inputValues['initialMaxTickets'] : DEFAULTMAXTICKETS);

        //Set all seats as empty
        $this->seats = array_fill(0,$rows,0);
        for($x = 0; $x < $rows; $x++)
            $this->seats[$x] = array_fill(0,$cols,0);
        
        //If we have an input block, process it
        if(isset($inputValues['seatingChartInput']))
            $this->processInput($inputValues['seatingChartInput']);

    }
    
    //Process input block. Accepts a string as defined in the problem statement
    public function processInput($seatingInfo) {
        
            //Sepearate each line
            $info_array = explode("\n",trim($seatingInfo));
            
            //Process initial reservations
            if(isset($info_array[0]) && strlen(trim($info_array[0])) > 0) {
                if($this->reserve_initial($info_array[0]) == 1)
                    array_shift($info_array);
            }
            
            //Process subsequent reservations
            foreach($info_array as $this_reservation) {
                $this->make_reservation($this_reservation);
            }

    }
    
    //Set initial reservations
    private function reserve_initial($seating_info) {
        
        if(strlen($seating_info) > 1 && strtoupper(substr($seating_info,0,1)) != "R")
            return -1;
        
        $requests = explode(" ",trim($seating_info));
        
        foreach($requests as $this_request) {
            $tmp = sscanf(strtoupper($this_request),"R%DC%D",$rows,$cols);
            $this->reserve_seat($rows-1,$cols-1,1);
        }
        
        return 1;
    }
    
    //Pass in a quantity to attempt to make a reservation for that many tickets
    public function make_reservation($quantity) {
        
        $result = "Not Available";
        
        //No buying too much!!
        if($quantity > (int)$this->maxRequest) {
            array_push($this->reservation_results,"Not Available");
            return $result;
        }
        
        $x = 0;
        //Set aside Manhattan values to test against to find best fit
        $current_manhattan_sum = floatval($quantity * (count($this->seats) + count($this->seats[0])));
        $current_x = -1;
        $current_y = -1;
        
        //Check each row to see if it has enough seats
        foreach($this->seats as $this_row) {
            for($y = 0; $y + $quantity < count($this_row); $y++) {
                //If we have 'quantity' consecutive open seats, calculate its Manhattan distance
                if(array_sum(array_slice($this_row,$y,$quantity)) == 0) {
                    $mc_temp = floatval(0);
                    for($n = 0; $n < $quantity; $n++)
                        $mc_temp += floatval((abs((count($this_row)+1) / 2 - ($y+1+$n))) + $x);
                    //If our Manhattan distance is lower, set it as the new candidate
                    if($mc_temp < $current_manhattan_sum) {
                        $current_manhattan_sum = $mc_temp;
                        $current_x = $x;
                        $current_y = $y;
                    }
                }
            }
            $x++;
        }
        
        //If we found open seats, reserve the seats and record the event
        if($current_x != -1) {
            $result = "R" . ($current_x+1) . "C" . ($current_y+1);
            
            for($i = 0; $i < $quantity; $i++) {
                $this->reserve_seat($current_x,$current_y+$i);
            }
                    
            if($quantity > 1)
                $result .= " - R" .  ($current_x+1) . "C" . ($current_y+$quantity);
        }
        
        array_push($this->reservation_results,$result);
        return $result;
    }
    
    //Pass in an x/y coordinate, and a reservation type (0 == open, 1 == initial, 2 == best available) to set that seat as the type
    public function reserve_seat($x,$y,$type=2) {
            $this->seats[$x][$y] = $type;
    }
    
    //Return the current seat configuration
    public function getSeats() { return $this->seats; }
    
    //Return an available seat count
    public function getAvailableSeatCount() {
        $free_count = 0;
        foreach($this->seats as $this_row) {
            $countValues = array_count_values($this_row);
            if(isset($countValues[0]))
                $free_count += $countValues[0];
        }
        return $free_count;
    }
    
    //Pull the reservation request results log as an array
    public function getReservationResults() { return $this->reservation_results; }
    
    //Make the output match the problem statement, to make it easier to talk about :)
    public function getPrettyOutput() {
        
        $output = "<pre>";
        
        foreach($this->reservation_results as $this_result)
            $output .= $this_result . "<br />";
            
        $output .= $this->getAvailableSeatCount() . "<br />";

        $output .= "<br />";
        
        foreach($this->seats as $this_row) {
            $output .= implode(" ",$this_row) . "<br />";
        }
        
        $output .= "<br />key:<br />&nbsp;&nbsp;0 == Available Seat<br />&nbsp;&nbsp;1 == Initial Reservation<br />&nbsp;&nbsp;2 == Reserved by Best Available";
        $output .= "</pre>";
        
        return $output;
    }

}
