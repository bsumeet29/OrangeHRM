<?php

/*
 * code by Rahul
 */
class CompOffListHeader extends ListHeader {
    public function  __construct() {
        $this->elementTypes[] = 'leaveListAction';
        $this->elementTypes[] = 'leaveComment';
        $this->elementTypes[] = 'leaveListBalance';
    }
}