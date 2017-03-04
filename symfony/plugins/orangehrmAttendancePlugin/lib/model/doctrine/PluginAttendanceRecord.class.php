<?php

/**
 * PluginAttendanceRecord
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginAttendanceRecord extends BaseAttendanceRecord {

    const STATE_PUNCHED_IN = "PUNCHED IN";
    const STATE_PUNCHED_OUT = "PUNCHED OUT";
    const STATE_CREATED = "CREATED";
    const STATE_INITIAL = "INITIAL";
    const STATE_NA = "NA";

    private $total = '';

    public function setTotal($total) {
        $this->total = $total;
    }

    public function getDuration() {
        $duration = '0';

        if ($this->getPunchOutUtcTime() != null) {
            $duration = round((strtotime($this->getPunchOutUtcTime()) - strtotime($this->getPunchInUtcTime())) / 3600, 2);
        }

        if ($this->getPunchInUtcTime() == null && $this->getPunchOutUtcTime() == null) {
            $duration = '---';
        }

        return $duration;
    }

    public function getTotal() {

        return $this->total;
    }

    public function getPunchInUserTimeAndZone() {

        $value = __('No attendance records to display');
        if ($this->getPunchInUserTime()) {
            $inUserTimeArray = explode(" ", $this->getPunchInUserTime());
            $value = set_datepicker_date_format($inUserTimeArray[0]) . " " . $inUserTimeArray[1] . '<span class="helpText"> GMT ' . $this->getPunchInTimeOffset() . '</span>';
        }

        return $value;
    }

    public function getPunchOutUserTimeAndZone() {

        $value = '';
        if ($this->getPunchOutUserTime()) {
            $outUserTimeArray = explode(" ", $this->getPunchOutUserTime());
            $value = set_datepicker_date_format($outUserTimeArray[0]) . " " . $outUserTimeArray[1] . '<span class="helpText"> GMT ' . $this->getPunchOutTimeOffset() . '</span>';
        }

        return $value;
    }

}