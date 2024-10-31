<?php

class scriptSeqManipulate {
    public $buffer = null;

    function setBuffer($buffer) {
        $this->buffer = $buffer;
    } 

    function appendAfter($scriptMatch, $match) {
        $mainScript = $this->getScript($scriptMatch);
        $html = str_get_html($this->buffer);
        $scriptTags = $html->find('script');
        $matchFound = false;

        foreach ($scriptTags as $script) {
            if (strpos($script->outertext, $match) !== false) {
                $script->outertext = $script->outertext . $mainScript;
                $matchFound = true;
                break;
            }
        }


        $this->buffer = $html->save();
        $html->clear();
        unset($html);

        // if ($matchFound) {
        //     $this->removeScript($scriptMatch);
        // }
    }

    function appendBefore($scriptMatch, $match) {
        $mainScript = $this->getScript($scriptMatch);
        $html = str_get_html($this->buffer);
        $scriptTags = $html->find('script');
        $matchFound = false;

        foreach ($scriptTags as $script) {
            if (strpos($script->outertext, $match) !== false) {
                $script->outertext = $mainScript . $script->outertext;
                $matchFound = true;
                break;
            }
        }
        
        
        $this->buffer = $html->save();
        $html->clear();
        unset($html);

        // if ($matchFound) {
        //     $this->removeScript($scriptMatch);
        // }
    }

    function getScript($scriptMatch) {
        $html = str_get_html($this->buffer);
        $scriptTags = $html->find('script');
        $found_tag = null;

        foreach ($scriptTags as $script) {
            if (strpos($script->outertext, $scriptMatch) !== false) {
                $found_tag = $script->outertext;
                $script->outertext = '';
                break;
            }
        }

        $this->buffer = $html->save();
        $html->clear();
        unset($html);

        return $found_tag;
    }

    function removeScript($match) {
        $html = str_get_html($this->buffer);
        $scriptTags = $html->find('script');

        foreach ($scriptTags as $script) {
            if (strpos($script->outertext, $match) !== false) {
                $script->outertext = '';
                break;
            }
        }

        $this->buffer = $html->save();
        $html->clear();
        unset($html);
    }

    function getBuffer() {
        return $this->buffer;
    }

    function clean() {
        $this->buffer = null;
    }
}