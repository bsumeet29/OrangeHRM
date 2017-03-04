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
class assignLeaveAction extends baseLeaveAction {

    protected $compOffService;
    protected $compoffApplicationService;

    public function getCompOffService() {
        if (!($this->compOffService instanceof CompOffService)) {
            $this->compOffService = new CompOffService();
        }

        return $this->compOffService;
    }

    public function setCompOffService(CompOffService $compOffService) {
        $this->compOffService = $compOffService;
    }

    public function getCompoffApplicationService() {
        if (!($this->compoffApplicationService instanceof CompoffApplicationService)) {
            $this->compoffApplicationService = new CompoffApplicationService();
        }
        return $this->compoffApplicationService;
    }

    public function setCompoffApplicationService(CompoffApplicationService $service) {
        $this->compoffApplicationService = $service;
    }

    public function execute($request) {
        $this->form = $this->getAssignLeaveForm($this->leaveTypes);
 //       echo"<pre>";print_r($this->form);exit;
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                try {

                    $compOffParameter = $this->getCompOffParameterObject($this->form->getValues(), $_POST['task']);
                    $success = $this->getCompoffApplicationService()->applyCompoff($compOffParameter);
                    if ($success) {
                        $this->getUser()->setFlash('success', __('Successfully Applied for comp off'));
//                                            $this->redirect('leave/viewMyCompOffList');
                        header("Location:viewMyCompOffList/reset/1");
                        die();

                    } else {
                        $this->overlapCompoff = $this->getCompoffApplicationService()->getOverlapCompoff();
                        $this->getUser()->setFlash('warning', __('Failed to Submit. Overlap Comp off'));
                    }
                } catch (LeaveAllocationServiceException $e) {
                    $this->getUser()->setFlash('warning', __($e->getMessage()));
                    $this->overlapCompoff = $this->getCompoffApplicationService()->getOverlapCompoff();
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
    protected function getCompOffParameterObject(array $formValues, $tasks) {
        $empData = $formValues['txtEmployee'];
        $formValues['txtEmpID'] = $empData['empId'];
        $time = $formValues['time'];
        $formValues['txtFromTime'] = $time['from'];
        $formValues['txtToTime'] = $time['to'];
        $formValues['tasks'] = $tasks;
        return new CompOffParameterObject($formValues);
    }

    /**
     * Get the Assign leave form.
     */
    protected function getAssignLeaveForm($leaveTypes) {

        $leaveFormOptions = array('leaveTypes' => $leaveTypes);
        $form = new AssignLeaveForm(array(), $leaveFormOptions, true);

        return $form;
    }

}
