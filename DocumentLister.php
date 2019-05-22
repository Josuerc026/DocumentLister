<?php
#
# DocumentLister
# PHP Class that creates an HTML list representation of a given directory
# with illimitable sub-directory support
#

class Document_Lister
{
    public $full_list = null;
    public function __construct($path, $name = '')
    {
        $this->full_list = $this->create_list($path, $name);
    }

    // Start point for creating unordered list
    public function create_list($path, $name)
    {
        if($this->dir_is_empty($path)){
            return false;
        }
        // Get Documents method gets called until
        // a directory iterates through all docs
        $list = '<ul class="user-guide-list">';
        $list .= $name ? '<li class="dir-label">' . $name . '</li>' : '';
        $list .= $this->get_documents($path);
        $list .= '</ul>';
        return $list;
    }

    // Gets documents within a given directory
    private function get_documents($path)
    {
        $list = '';
        $dir = scandir($path);
        foreach($dir as $doc){
            if($doc !== '.' && $doc !== '..'){
                $current_path = $path . '/'. $doc;
                // We recursively call Create List if the item
                // within the directory is a sub-directory
                if(is_dir($path . '/'. $doc)){
                    $list .= $this->create_list($current_path, $doc);
                }else{
                    // Otherwise, create a list item for the current file
                    // if it has any content within it
                    if($this->have_contents($current_path)){
                        $list .= '<li data-path="'. $current_path .'">';
                        $list .= ($this->get_document_title($current_path) ? $this->get_document_title($current_path) : $doc);
                        $list .= '</li>';
                    }
                }
            }
        }
        return $list;
    }

    // Checking the file contents of the given file path
    function have_contents($file_path)
    {
        return (boolean) strlen(file_get_contents(realpath($file_path)));
    }

    // Extracting the first line from the document
    // to use as a title
    private function get_document_title($file_path)
    {
        // Opening the file and getting the first line
        $line = fgets(fopen($file_path, 'r'));

        // Only returning back the text if content doesn't include whitespace
        return trim($ps->text($line)) ? strip_tags($line) : false;
    }

    // Checks if the directory is empty or doesn't exist
    private function dir_is_empty($dir)
    {
        // Returns true if the directory isn't found
        if(!file_exists($dir)) return true;
        //If the directory can iterate through files then it isn't empty
        $handle = opendir($dir);
        while (false !== ($doc = readdir($handle))) {
            if ($doc != "." && $doc != "..") {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        // Return true if nothing is inside the directory
        return true;
    }
    
    // Access to finalized list of all files/sub-directories
    public function get_list()
    {
        return $this->full_list;
    }
}