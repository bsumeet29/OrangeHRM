<?php

class CompOffListActionCell extends SelectSingleCell {

    public function __toString() {

        $statusDiffer = ($this->dataObject->isStatusDiffer());
        if ($statusDiffer) {
            return content_tag('a', __('Go to Detailed View'), array(
                'href' => url_for('leave/viewCompOffRequest?id=' . $this->dataObject->getId()),
            ));
        } else {
            return parent::__toString();
        }
    }

}
