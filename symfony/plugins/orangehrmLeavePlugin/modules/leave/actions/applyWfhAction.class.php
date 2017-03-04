<?php

/*
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

/**
 * Action used to assign Leave to an Employee
 */
class applyWfhAction extends baseLeaveAction {

    protected $wfhService;
    protected $wfhApplicationService;

    public function getCompOffService() {
        if (!($this->compOffService instanceof CompOffService)) {
            $this->compOffService = new CompOffService();
        }

        return $this->compOffService;
    }

    public function setWfhService(CompOffService $compOffService) {
        $this->wfhService = $compOffService;
    }

    public function getWfhApplicationService() {
        if (!($this->wfhApplicationService instanceof WfhApplicationService)) {
            $this->wfhApplicationService = new WfhApplicationService();
        }
        return $this->wfhApplicationService;
    }

    public function setWfhApplicationService(WfhApplicationService $service) {
        $this->wfhApplicationService = $service;
    }

    public function execute($request) {
        $this->form = $this->getApplyWfhForm($this->leaveTypes);
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                try {
                    $wfhParameter = $this->getWfhParameterObject($this->form->getValues(), $_POST['task']);
                    $success = $this->getWfhApplicationService()->applyWfh($wfhParameter);
                    if ($success) {
                        $this->getUser()->setFlash('success', __('Successfully Applied for WFH'));
//                        $this->redirect('leave/viewMyWfhList/reset/1');
                        header("Location:viewMyWfhList/reset/1");
                        die();

                    } else {
                        $this->overlapWfh = $this->getWfhApplicationService()->getOverlapWfh();
                        $this->getUser()->setFlash('warning', __('Failed to Submit. Overlap WFH'));
                    }
                } catch (LeaveAllocationServiceException $e) {
                    $this->getUser()->setFlash('warning', __($e->getMessage()));
                    $this->overlapWfh = $this->getWfhApplicationService()->getOverlapWfh();
                    $this->workshiftLengthExceeded = FALSE;
                }
            }
        }
    }

    /**
     * @todo Move to form?
     * @param array $formValues
     * @return LeaveParameterObject
     */
    protected function getWfhParameterObject(array $formValues, $tasks) {
        $empData = $formValues['txtEmployee'];
        $formValues['txtEmpID'] = $empData['empId'];
        $time = $formValues['time'];
        $formValues['txtFromTime'] = $time['from'];
        $formValues['txtToTime'] = $time['to'];
        $formValues['tasks'] = $tasks;
        return new WfhParameterObject($formValues);
    }

    /**
     * Get the Assign leave form.
     */
    protected function getApplyWfhForm($leaveTypes) {

        $leaveFormOptions = array('leaveTypes' => $leaveTypes);
        $form = new ApplyWfhForm(array(), $leaveFormOptions, true);

        return $form;
    }

}
