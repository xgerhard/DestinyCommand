<?php
namespace App\Command;

class Query
{
    public $reqUser = false;
    public $actions = [];
    public $gamertags = [];
    public $consoles = [];

    public function __construct($strQuery)
    {
        $aConsoles = array("xbox" => 1, "ps" => 2, "xb" => 1, "xb1" => 1, "psn" => 2, "playstation" => 2, "ps4" => 2, "pc" => 4, "bnet" => 4);

        // Seperate query by spaces
        $strQuery = urldecode($strQuery);
        $strQuery = str_replace(array(",", "%20"), array(";", " "), $strQuery); // Use 1 seperator
        $aQuery = explode(" ", $strQuery);
        $aQuery = array_diff($aQuery, array(''));

        // Count 1 means its only one 'action', for example '!destiny lvl', where 'lvl' is the action)
        if(count($aQuery) === 1 && false)
        {
            $oAction = new Action($aQuery[0]);
            if(!isset($oAction->noUser) || $oAction->noUser === false) $this->reqUser = true;
            $this->actions[] = $oAction;
        }
        else
        {
            // First we loop to merge actions or gamertags together
            foreach($aQuery AS $i => $strQueryPart)
            {
                if(isset($aQuery[$i+1]) && (substr($strQueryPart, -1) === ';' || $aQuery[$i+1][0] === ";"))
                {
                    $aQuery[$i+1] = $aQuery[$i+1] . $strQueryPart;
                    unset($aQuery[$i]);
                }
            }

            // Last parameter is the console
            if(in_array(end($aQuery), array_keys($aConsoles)))
            {
                $this->consoles[] = $aConsoles[strtolower(array_pop($aQuery))];
            }

            // First parameter is the action
            if(!empty($aQuery))
            {
                $aActions = array_unique(explode(";", strtolower(array_shift($aQuery))));
                foreach($aActions AS $strAction)
                {
                    $oAction = new Action($strAction);
                    if(!isset($oAction->noUser) || $oAction->noUser === false) $this->reqUser = true;
                    $this->actions[$oAction->key] = $oAction;
                    
                }               
            }

            // Whats  left should be gamertags
            if(!empty($aQuery))
            {
                $this->gamertags = explode(";", implode(" ", $aQuery));
                if(!empty($this->gamertags))
                {
                    // You can specify a platform for each gamertag seperately, read them here and overwrite the overall platform
                    foreach($this->gamertags AS $i => $strGamertag)
                    {
                        $aGamertag = explode(":", $strGamertag);
                        if(count($aGamertag == 2))
                        {
                            $this->gamertags[$i] = $aGamertag[0];
                            if(isset($aGamertag[1]) && in_array($aGamertag[1], array_keys($aConsoles))) $this->consoles[$i] = $aConsoles[$aGamertag[1]];
                        }
                    }
                }
            }
        }
    }
}
?>