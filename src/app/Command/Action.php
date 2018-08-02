<?php
namespace App\Command;

class Action
{
    public function __construct($strAction)
    {
        $aAction = false;
        $strAction = $this->getAlias($strAction);
        $aFunctions = array("isTextCommand", "isTrialsReportCommand", "isGearCommand", "isStatCommand");
        foreach($aFunctions AS $strFunction)
        {
            $aAction = $this->{$strFunction}($strAction);
            if($aAction !== false) break;
        }

        if($aAction == false || is_null($aAction)) 
        {
            $aAction = $this->isTextCommand('default_info');
        }
        foreach($aAction AS $k => $v)
        {
            $this->$k = $v;
        }
    }

    private function isTrialsReportCommand($strAction)
    {
        $aTrialsReportActions = array(
            "trialsteam" => "getFireteam",
            "tt" => "getFireteam"
        );

        if(isset($aTrialsReportActions[$strAction]))
        {
            return array(
                'key' => 'TrialsTeam',
                'title' => 'TrialsTeam',
                'provider' => 'TrialsReportProvider',
                'endpoint' => $aTrialsReportActions[$strAction],
                'filter' => 'getFireteamStats'
            );
        }
        return false;
    }

    private function isGearCommand($strAction)
    {
        $aItemActions = array(
            'ghost',
            'vehicle',
            'ship',
            'clan',
            'classitem',
            'emblem',
            'emote',
            'aura',
            'subclass',
            'primary',
            'secondary', 
            'heavy',
            'helmet',
            'gauntlet',
            'legs',
            'chest',
            'weapons' => array('primary', 'secondary', 'heavy'),
            'gear' => array('helmet', 'chest', 'legs')
        );

        foreach($aItemActions AS $xKey => $xItemAction)
        {
            $c = false;
            if(is_array($xItemAction) && $xKey == $strAction)
            {
                $strTitle = $xKey;
                $bPerks = false;
                $xField = $xItemAction;
                $c = true;
            }
            elseif($strAction == $xItemAction)
            {
                $bPerks = true;
                $strTitle = $strAction;
                $xField = array($strAction);
                $c = true;
            }

            if($c)
            {
                return array(
                    'key' => $strTitle,
                    'title' => $strTitle,
                    'provider' => 'BungieProvider',
                    'endpoint' => 'profile',
                    'filter' => 'getCharacterEquipment',
                    'options' => (object) array(
                        'perks' => $bPerks,
                        'params' => array(
                            'components' => array(205, 305, 300),
                        ),
                        'latest' => true,
                        'field' => $xField
                    )
                );
            }
        }
        return false;
    }

    private function isStatCommand($strAction)
    {
        $c = false;
        $aStatActions = array(
            'games' => 'activitiesEntered',
            'wins' => 'activitiesWon',
            'assists' => 'assists',
            'tdd' => 'totalDeathDistance',
            'avgdd' => 'averageDeathDistance',
            'tkd' => 'totalKillDistance',
            'avgkd' => 'averageKillDistance',
            'time' => 'secondsPlayed',
            'deaths' => 'deaths',
            'kills' => 'kills',
            'avgls' => 'averageLifespan',
            'score' => 'score',
            'avgspk' => 'averageScorePerKill',
            'avgspl' => 'averageScorePerLife',
            'mk' => 'bestSingleGameKills',
            'bestscore' => 'bestSingleGameScore',
            'kd' => 'killsDeathsRatio',
            'kda' => 'killsDeathsAssists',
            'pkills' => 'precisionKills',
            'res' => 'resurrectionsPerformed',
            'resres' => 'resurrectionsReceived',
            'suicides' => 'suicides',
            'fusion' => 'weaponKillsFusionRifle',
            'handcannon'=> 'weaponKillsHandCannon',
            'auto' => 'weaponKillsAutoRifle',
            'machinegun' => 'weaponKillsMachinegun',
            'melee' => 'weaponKillsMelee',
            'pulse' => 'weaponKillsPulseRifle',
            'rocket' => 'weaponKillsRocketLauncher',
            'scout' => 'weaponKillsScoutRifle',
            'shotgun' => 'weaponKillsShotgun',
            'sniper' => 'weaponKillsSniper',
            'smg' => 'weaponKillsSubmachinegun',
            'relic' => 'weaponKillsRelic',
            'sidearm' => 'weaponKillsSideArm',
            'sword' => 'weaponKillsSword',
            'akills' => 'weaponKillsAbility',
            'grenade' => 'weaponKillsGrenade',
            'grenadelauncher' => 'weaponKillsGrenadeLauncher',
            'bestwep' => 'weaponBestType',
            'wl' => 'winLossRatio',
            'lks' => 'longestKillSpree',
            'lsl' => 'longestSingleLife',
            'mpk' => 'mostPrecisionKills',
            'orbs' => 'orbsDropped',
            'orbsg' => 'orbsGathered',
            'cr' => 'combatRating',
            'fastest' => 'fastestCompletionMs',
            'lkd' => 'longestKillDistance',
        );
        
        $aStatMedals = array(
            'hurricane' => 'medalAbilityFlowwalkerMulti',
            'handfullofbullets' => 'medalAbilityGunslingerMulti',
            'lethalinstinct' => 'medalAbilityGunslingerQuick',
            'lightningstorm' => 'medalAbilityStormcallerMulti',
            'bloodforblood' => 'medalAvenger',
            'iliveherenow' => 'medalControlAdvantageHold',
            'flagbearer' => 'medalControlMostAdvantage',
            'gangsallhere' => 'medalCountdownRoundAllAlive',
            'thecycle' => 'medalCycle',
            'dodgethis' => 'medalDefeatHunterDodge',
            'barricadebreaker' => 'medalDefeatTitanBrace',
            'riftbreaker' => 'medalDefeatWarlockSigil',
            'notonmywatch' => 'medalDefense',
            'crushedthem' => 'medalMatchBlowout',
            'fightme' => 'medalMatchMostDamage',
            'timeandahalf' => 'medalMatchOvertime',
            'undefeated' => 'medalMatchUndefeated',
            'doubleplay' => 'medalMulti2x',
            'tripleplay' => 'medalMulti3x',
            'lightsout' => 'medalMulti4x',
            'annihilation' => 'medalMultiEntireTeam',
            'bestservedcold' => 'medalPayback',
            'quickstrike' => 'medalQuickStrike',
            'unyielding' => 'medalStreak10x',
            'ruthless' => 'medalStreak5x',
            'weranoutofmedals' => 'medalStreakAbsurd',
            'combinedfire' => 'medalStreakCombined',
            'shutdown' => 'medalStreakShutdown',
            'wreckingcrew' => 'medalStreakTeam',
            'notsofastmyfriend' => 'medalSuperShutdown',
            'mycrestismyown' => 'medalSupremacyNeverCollected',
            'safeandsecured' => 'medalSupremacySecureStreak',
            'survivor' => 'medalSurvivalUndefeated',
            'assaultspecialist' => 'medalWeaponAuto',
            'coldfusion' => 'medalWeaponFusion',
            'directhit' => 'medalWeaponGrenade',
            'hawkeye' => 'medalWeaponHandCannon',
            'lethalcadence' => 'medalWeaponPulse',
            'splashdamage' => 'medalWeaponRocket',
            'fieldscout' => 'medalWeaponScout',
            'closeencounters' => 'medalWeaponShotgun',
            'submachinist' => 'medalWeaponSmg',
            'regent' => 'medalWeaponSword',
            'neverindoubt' => 'medalMatchNeverTrailed',
            'fromthejawsofdefeat' => 'medalMatchComeback',
            'fallingstar' => 'medalAbilityDawnbladeSlam',
            'defyinggravity' => 'medalAbilityDawnbladeAerial',
            'singularity' => 'medalAbilityVoidwalkerVortex',
            'fromdowntown' => 'medalAbilityVoidwalkerDistance',
            'thunderstruck' => 'medalAbilityStormcallerLandfall',
            'lightningstrike' => 'medalAbilityFlowwalkerQuick',
            'entangled' => 'medalAbilityNightstalkerTetherQuick',
            'longbow' => 'medalAbilityNightstalkerLongRange',
            'perfectguard' => 'medalAbilitySentinelWard',
            'flyingfortress' => 'medalAbilitySentinelCombo',
            'absoluteforce' => 'medalAbilityJuggernautSlam',
            'strikerspecial' => 'medalAbilityJuggernautCombo',
            'pitchperfect' => 'medalAbilitySunbreakerLongRange',
            'everythinglookslikeanail' => 'medalAbilitySunbreakerMulti',
            'counterattack' => 'medalCountdownDefense',
            'pyrotechnics' => 'medalCountdownDetonated',
            'bombswhatbombs' => 'medalCountdownDefusedMulti',
            'laststand' => 'medalCountdownDefusedLastStand',
            'perfectgame' => 'medalCountdownPerfect',
            'lonegun' => 'medalSurvivalWinLastStand',
            'minutetowinit' => 'medalSurvivalQuickWipe',
            'undertaker' => 'medalSurvivalKnockout',
            'accordingtoplan' => 'medalSurvivalComeback',
            'untouchable' => 'medalSurvivalTeamUndefeated',
            'reclaimer' => 'medalControlPerimeterKill',
            'dominantadvantage' => 'medalControlAdvantageStreak',
            'poweroverwhelming' => 'medalControlPowerPlayWipe',
            'firstsecure' => 'medalSupremacyFirstCrest',
            'steadfastally' => 'medalSupremacyRecoverStreak',
            'crestfallen' => 'medalSupremacyCrestCreditStreak',
            'acrownofcrests' => 'medalSupremacyPerfectSecureRate',
            'lightemup' => 'medalMayhemFirstSuper',
            'fireinthehole' => 'medalMayhemGrenadeStreak',
            'punchandpie' => 'medalMayhemMeleeStreak',
            'superstar' => 'medalMayhemCastStreak',
            'byourpowerscombined' => 'medalMayhemCastMulti',
            'totalmayhem' => 'medalMayhemKillStreak',
            'polyarmory' => 'medalCrimsonWeaponCombo',
            'thirdwheel' => 'medalCrimsonRevengeMulti',
            'brokenup' => 'medalCrimsonApartMulti',
            'heartbreaker' => 'medalCrimsonSuddenDeath',
            'bestinclass' => 'medalRumbleDefeatAllClasses',
            'assassin' => 'medalRumbleUnassistedStreak',
            'pickpocket' => 'medalRumbleStealStreak',
            'podiumfinish' => 'medalRumbleTop3',
            'roundrobin' => 'medalRumbleDefeatAllPlayers',
            'thesumofalltears' => 'medalRumbleBetterThanAllCombined',
            'slayer' => 'medalSlayer',
            'reaper' => 'medalStreak6x',
            'seventhcolumn' => 'medalStreak7x',
            'localmaxima' => 'medalShowdownMostKills',
            'denialofservice' => 'medalShowdownAmmoStreak',
            'clawingback' => 'medalShowdownRetakeLead',
            'whenthedustclears' => 'medalShowdownFullTeamSurvival',
            'werenotdoneyet' => 'medalShowdownForceFinalRound',
            'invincible' => 'medalShowdownUndefeated',
            'totalmedals' => 'allMedalsEarned'
        );

        $aPlaylists = array(
            'story' => 2,
            'strike' => 3,
            'raid' => 4,
            'pvp' => 5,
            'patrol' => 6,
            'pve' => 7,
            'control' => 10,
            'clash' => 12,
            'nightfall' => 16,
            'ib' => 19,
            'supremacy' => 31,
            'survival' => 37,
            'countdown' => 38,
            'trials' => 39,
            'social' => 40
        );

        $iModes = 5; // default PvP.
        $bPGA = false; // default false.
        $bSeperate = false; // default false.

        foreach($aPlaylists AS $strPlaylist => $iPlaylistModes)
        {
            if(strpos($strAction, $strPlaylist) !== false)
            {
                $iModes = $iPlaylistModes;
                $strAction = str_replace($strPlaylist, "", $strAction);

                if($strAction == '' || $strAction == 'c')
                {
                    $c = true;
                    $xField = array('killsDeathsRatio', 'winLossRatio', 'activitiesWon');
                    $strTitle = 'summary';
                    break;
                }
            }
        }

        if(isset($strAction[0]) && $strAction[0] == 'c' && strlen($strAction) > 2)
        {
            $bSeperate = true;
            $strAction = substr($strAction, 1);
        }

        if(strlen($strAction) > 3 && substr($strAction, -3) == 'pga')
        {
            $bPGA = true;
            $strAction = substr($strAction, 0, -3);
        }

        // check alias again, since we removed the pga and c part
        $strAction = $this->getAlias($strAction);
        $bMedal = false;
        if($strAction != "" && (isset($aStatActions[$strAction]) || isset($aStatMedals[$strAction])))
        {
            if(isset($aStatActions[$strAction]))
                $xStat = $aStatActions[$strAction];
            else
            {
                $xStat = $aStatMedals[$strAction];
                $bMedal = true;
            }

            if(is_array($xStat))
            {
                $strTitle = $strAction;
                $xField = $xStat;
                $c = true;
            }
            else
            {
                $strTitle = $strAction;
                $xField = array($xStat);
                $c = true;
            }
        }

        if($c)
        {
            return array(
                'key' => $strTitle,
                'title' => "",
                'provider' => 'BungieProvider',
                'endpoint' => 'stats',
                'filter' => 'getStats',
                'options' => (object) array(
                    'field' => $xField,
                    'modes' => $iModes,
                    'groups' => ($bMedal ? 'Medals' : 'General'),
                    'seperate' => $bSeperate,
                    'pga' => $bPGA
                )
            );
        }
    }

    private function isTextCommand($strAction)
    {
        $a = array(
            'default_info' => 'Usage !destiny <action> <user> <platform>, Command list: destinycommand.com, for help @DestinyCommand on Twitter',
            'help' => 'Usage !destiny <action> <user> <platform>, Command list: destinycommand.com, for help @DestinyCommand on Twitter',
            'commands' => 'Command list: destinycommand.com',
            'setplayer' => 'This feature will return with the full version later',
            'ratemybutt' => $this->RateMyButt(),
            'xur' => 'This command will return when Xur data is available in the Bungie API',
            'trialsmap' => '\'Trialsmap\' command is in development',
            'nightfall' => '\'Nightfall\' command is in development',
            'elo' => '\'ELO\' command is in development',
        );

        if(isset($a[$strAction]))
        {
            return array(
                'key' => $strAction,
                'text' => $a[$strAction],
                'provider' => 'plain_text',
                'noUser' => true
            );
        }
        return false;
    }

    private function RateMyButt()
    {
        $x = rand(1,10);
        $i = 10;
        if($x == 5)
        {
            $a = rand(1, 4);
            if($a == 1) $i = 7; // 5/7 ratings Kappa
        }
        return 'butt rated: '. $x .'/'. $i; 
    }

    private function getAlias($strAction)
    {
        $a = array(
            'tt' => 'trialsteam',
            'kinetic' => 'primary',
            'energy' => 'secondary',
            'power' => 'heavy',
            'loadout' => 'weapons',
            'rmb' => 'ratemybutt',
            'combatrating' => 'cr',
            'winloss' => 'wl',
            'mostkills' => 'mk',
            'nade' => 'grenade'
        );
        return isset($a[$strAction]) ? $a[$strAction] : $strAction;
    }
}
?>
