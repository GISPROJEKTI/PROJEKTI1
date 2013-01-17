<?php

class AppError extends ErrorHandler 
{
    public function pollNotFound()
    {
        $this->_outputMessage('poll_not_found');
    }

    public function pollNotPublished()
    {
        $this->_outputMessage('poll_not_published');
    }

    public function pollClosed()
    {
        $this->_outputMessage('poll_closed');
    }

    public function invalidHash()
    {
        $this->_outputMessage('invalid_hash');
    }

}