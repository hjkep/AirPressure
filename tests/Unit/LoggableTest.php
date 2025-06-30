<?php

use AirPressure\Loggable;

describe('loggable', function() {
    it('should log a message', function() {
        $c = new class { 
            use Loggable; 

            public function log(string $message) {
                $this->_log($message);
            }
        };

        $message = 'Something to capture?';

        ob_start();
        $c->log($message);
        $contents = ob_get_contents();
        ob_end_clean();
        expect($contents)->toContain($message);
    });
});