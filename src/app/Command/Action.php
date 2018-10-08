<?php
namespace App\Command;

class Action
{
    public function __construct($strAction)
    {
        $aAction = false;
        $strAction = $this->getAlias($strAction);
        $aFunctions = array("isTextCommand", "isTrialsReportCommand", "isGearCommand", "isCharacterProfileCommand", "isStatCommand");
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

    private function isCharacterProfileCommand($strAction)
    {
        if(isset($strAction[0]) && $strAction[0] == 'c' && strlen($strAction) > 2)
        {
            $strAction = substr($strAction, 1);
        }
 
        $aCharacterProfileActions = array(
            "powerlevel" => 'light'
        );

        if(isset($aCharacterProfileActions[$strAction]))
        {
            return array(
                'key' => $strAction,
                'title' => "",
                'provider' => 'BungieProvider',
                'endpoint' => 'profile',
                'filter' => 'getCharacterProfileValue',
                'options' => (object) array(
                    'field' => $aCharacterProfileActions[$strAction]
                )
            );
        }
        return false;
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
            /* General stats */
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
            /* Gambit only stats */
            'invasions' => 'invasions',
            'invasionkills' => 'invasionKills',
            'invaderkills' => 'invaderKills',
            'invaderdeaths' => 'invaderDeaths',
            'primevalkills' => 'primevalKills',
            'blockerkills' => 'blockerKills',
            'mobkills' => 'mobKills',
            'highvaluekills' => 'highValueKills',
            'motespickedup' => 'motesPickedUp',
            'motesdeposited' => 'motesDeposited',
            'motesdenied' => 'motesDenied',
            'motesdegraded' => 'motesDegraded',
            'moteslost' => 'motesLost',
            'bankoverage' => 'bankOverage',
            'smallblockers' => 'smallBlockersSent',
            'mediumblockers' => 'mediumBlockersSent',
            'largeblockers' => 'largeBlockersSent',
            'primevaldamage' => 'primevalDamage',
            'primevalhealing' => 'primevalHealing',
            'gbroundsplayed' => 'roundsPlayed',
            'gbroundswon' => 'roundsWon'
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
            'totalmedals' => 'allMedalsEarned',
            /* Gambit only medals */
            'armyofone' => 'medals_pvecomp_medal_invader_kill_four',
            'massacre' => 'medals_pvecomp_medal_massacre',
            'noescape' => 'medals_pvecomp_medal_no_escape',
            'blockbuster' => 'medals_pvecomp_medal_blockbuster',
            'motehavebeen' => 'medals_pvecomp_medal_tags_denied_15',
            //'' => 'medals_pvecomp_medal_bank_kill',
            'denied' => 'medals_pvecomp_medal_denied',
            'gbnotonmywatch' => 'medals_pvecomp_medal_invasion_shutdown',
            'locksmith' => 'medals_pvecomp_medal_locksmith',
            'blockparty' => 'medals_pvecomp_medal_block_party',
            'neversaydie' => 'medals_pvecomp_medal_never_say_die',
            'halfbanked' => 'medals_pvecomp_medal_half_banked',
            'takingturns' => 'medals_pvecomp_medal_everyone_invaded',
            'killmonger' => 'medals_pvecomp_medal_killmonger',
            'thrillmonger' => 'medals_pvecomp_medal_thrillmonger',
            'overkillmonger' => 'medals_pvecomp_medal_overkillmonger',
            'valuehunter' => 'medals_pvecomp_medal_value_hunter',
            'firsttoblock' => 'medals_pvecomp_medal_first_to_block',
            'fastfill' => 'medals_pvecomp_medal_fast_fill',
            'biggamehunter' => 'medals_pvecomp_medal_big_game_hunter',
            //'' => 'medals_pvecomp_medal_kill_after_invasion',
            'payback' => 'medals_pvecomp_medal_revenge',
            'rapidpayback' => 'medals_pvecomp_medal_revenge_same_invasion',
            'titansmash' => 'medals_pvecomp_medal_fist_of_havoc_multikill',
            'atitancanfly' => 'medals_pvecomp_medal_meteor_strike_multikill',
            'wardofdawn' => 'medals_pvecomp_medal_ward_of_dawn_blocking',
            'captainofthevoid' => 'medals_pvecomp_medal_void_shield_multikill',
            'atimeforhammers' => 'medals_pvecomp_medal_thermal_hammer_multikill',
            'burningpath' => 'medals_pvecomp_medal_thermal_maul_multikill',
            'lightningrod' => 'medals_pvecomp_medal_arc_staff_multikill',
            'fireslinger' => 'medals_pvecomp_medal_golden_gun_multikill',
            'fanofknives' => 'medals_pvecomp_medal_thermal_knives_multikill',
            'spectralsurgeon' => 'medals_pvecomp_medal_void_blade_multikill',
            'ensnarement' => 'medals_pvecomp_medal_void_bow_multikill',
            'ridelightning' => 'medals_pvecomp_medal_arc_lightning_multikill',
            'chaosincarnate' => 'medals_pvecomp_medal_arc_beam_multikill',
            'voidbaseddemolition' => 'medals_pvecomp_medal_nova_bomb_multikill',
            'voidunleashed' => 'medals_pvecomp_medal_nova_pulse_multikill',
            'rainoffire' => 'medals_pvecomp_medal_thermal_sword_multikill',
            'dugin' => 'Medals_pvecomp_medal_thermal_sword_healing_multikill'
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
            'social' => 40,
            'rumble' => 48,
            'doubles' => 50,
            'gambit' => 63
        );

        $iModes = 5; // default PvP.
        $bPGA = false; // default false.
        $bSeperate = false; // default false.

        $aGambitStats = array(
            'invasions',
            'invasionkills',
            'invaderkills',
            'invaderdeaths',
            'primevalkills',
            'blockerkills' ,
            'mobkills',
            'highvaluekills',
            'motespickedup',
            'motesdeposited',
            'motesdenied',
            'motesdegraded' ,
            'moteslost',
            'bankoverage',
            'smallblockers',
            'mediumblockers',
            'largeblockers',
            'primevaldamage',
            'primevalhealing',
            'gbroundsplayed',
            'gbroundswon',

            'armyofone',
            'massacre' ,
            'noescape',
            'blockbuster',
            'motehavebeen' ,
            'denied',
            'gbnotonmywatch',
            'locksmith',
            'blockparty',
            'neversaydie',
            'halfbanked',
            'takingturns',
            'killmonger',
            'thrillmonger',
            'overkillmonger',
            'valuehunter',
            'firsttoblock',
            'fastfill',
            'biggamehunter',
            'payback',
            'rapidpayback',
            'titansmash',
            'atitancanfly',
            'wardofdawn',
            'captainofthevoid',
            'atimeforhammers',
            'burningpath',
            'lightningrod',
            'fireslinger',
            'fanofknives',
            'spectralsurgeon',
            'ensnarement',
            'ridelightning',
            'chaosincarnate',
            'voidbaseddemolition',
            'voidunleashed',
            'rainoffire',
            'dugin'
        );

        if(in_array($strAction, $aGambitStats) || in_array(substr($strAction, 1), $aGambitStats))
        {
            $iModes = 63; // These stats only will work for Gambit
        }
        else
        {
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
            'default_info' => 'Usage !destiny <action> <user> <platform>, Command list: destinycommand.com for help @DestinyCommand on Twitter',
            'help' => 'Usage !destiny <action> <user> <platform>, Command list: destinycommand.com for help @DestinyCommand on Twitter',
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
            'nade' => 'grenade',
            'powerlvl' => 'powerlevel',
            'light' => 'powerlevel',
            'pwrlvl' => 'powerlevel'
        );
        return isset($a[$strAction]) ? $a[$strAction] : $strAction;
    }
}
?>
