<?php
namespace App\Destiny;

class Stat
{
    public $key;
    public $value;
    public $displayValue;
    public $title;

    public function __construct($oStat, $strPlaylist)
    {
        $this->key = $oStat->statId;
        $this->value = $oStat->basic->value;
        $this->displayValue = $oStat->basic->displayValue;
        $this->title = $this->getTitle($this->key);
        $this->playlist = $strPlaylist;
    }

    public function getTitle($strKey)
    {
        $aTitles = array(
            /* Stats */
            'activitiesEntered' => 'games played',
            'activitiesWon' => 'wins',
            'assists' => 'assists',
            'totalDeathDistance' => 'total death distance',
            'averageDeathDistance' => 'average death distance',
            'totalKillDistance' => 'total kill distance',
            'averageKillDistance' => 'average kill distance',
            'secondsPlayed' => 'time played',
            'deaths' => 'deaths',
            'kills' => 'kills',
            'averageLifespan' => 'average lifespan',
            'score' => 'score',
            'averageScorePerKill' => 'average score per kill',
            'averageScorePerLife' => 'average score per life',
            'bestSingleGameKills' => 'most kill one game',
            'bestSingleGameScore' => 'best gamescore',
            'killsDeathsRatio' => 'K/D',
            'killsDeathsAssists' => 'K+A/D',
            'precisionKills' => 'precision kills',
            'resurrectionsPerformed' => 'resurrections performed',
            'resurrectionsReceived' => 'resurrections received',
            'suicides' => 'suicides',
            'weaponKillsFusionRifle' => 'fusion rifle kills',
            'weaponKillsHandCannon' => 'handcannon kills',
            'weaponKillsAutoRifle' => 'auto rifle kills',
            'weaponKillsMachinegun' => 'machinegun kills',
            'weaponKillsPulseRifle' => 'pulse rifle kills',
            'weaponKillsRocketLauncher' => 'rocket launcher kills',
            'weaponKillsScoutRifle' => 'scout rifle kills',
            'weaponKillsShotgun' => 'shotgun kills',
            'weaponKillsSniper' => 'sniper kills',
            'weaponKillsSubmachinegun' => 'submachinegun kills',
            'weaponKillsRelic' => 'relic kills',
            'weaponKillsSideArm' => 'side arm kills',
            'weaponKillsSword' => 'sword kills',
            'weaponKillsAbility' => 'ability kills',
            'weaponKillsGrenade' => 'grenade kills',
            'weaponKillsGrenadeLauncher' => 'grenade launcher kills',
            'weaponKillsMelee' => 'melee kills',
            'weaponBestType' => 'best weapon type',
            'winLossRatio' => 'W/L',
            'longestKillSpree' => 'longest kill spree',
            'longestSingleLife' => 'longest single life',
            'mostPrecisionKills' => 'most precision kills',
            'orbsDropped' => 'orbs dropped',
            'orbsGathered' => 'orbs gathered',
            'combatRating' => 'combat rating',
            'fastestCompletionMs' => 'fastest completion',
            'longestKillDistance' => 'longest kill distance',
            /* Medals */
            'medalAbilityFlowwalkerMulti' => 'Hurricane',
            'medalAbilityGunslingerMulti' => 'Handfull of Bullets',
            'medalAbilityGunslingerQuick' => 'Lethal Instinct',
            'medalAbilityStormcallerMulti' => 'Lightning Storm',
            'medalAvenger' => 'Blood for Blood',
            'medalControlAdvantageHold' => 'I Live Here Now',
            'medalControlMostAdvantage' => 'Flagbearer',
            'medalCountdownRoundAllAlive' => 'Gangs All Here',
            'medalCycle' => 'The Cycle',
            'medalDefeatHunterDodge' => 'Dodge This',
            'medalDefeatTitanBrace' => 'Barricade Breaker',
            'medalDefeatWarlockSigil' => 'Rift Breaker',
            'medalDefense' => 'Not on My Watch',
            'medalMatchBlowout' => 'Crushed Them',
            'medalMatchMostDamage' => 'Fight Me!',
            'medalMatchOvertime' => 'Time and a Half',
            'medalMatchUndefeated' => 'Undefeated',
            'medalMulti2x' => 'Double Play',
            'medalMulti3x' => 'Triple Play',
            'medalMulti4x' => 'Lights Out',
            'medalMultiEntireTeam' => 'Annihilation',
            'medalPayback' => 'Best Served Cold',
            'medalQuickStrike' => 'Quick Strike',
            'medalStreak10x' => 'Unyielding',
            'medalStreak5x' => 'Ruthless',
            'medalStreakAbsurd' => 'We Ran Out of Medals',
            'medalStreakCombined' => 'Combined Fire',
            'medalStreakShutdown' => 'Shutdown',
            'medalStreakTeam' => 'Wrecking Crew',
            'medalSuperShutdown' => 'Not So Fast My Friend',
            'medalSupremacyNeverCollected' => 'My Crest Is My Own',
            'medalSupremacySecureStreak' => 'Safe and Secured',
            'medalSurvivalUndefeated' => 'Survivor',
            'medalWeaponAuto' => 'Assault Specialist',
            'medalWeaponFusion' => 'Cold Fusion',
            'medalWeaponGrenade' => 'Direct Hit',
            'medalWeaponHandCannon' => 'Hawkeye',
            'medalWeaponPulse' => 'Lethal Cadence',
            'medalWeaponRocket' => 'Splash Damage',
            'medalWeaponScout' => 'Field Scout',
            'medalWeaponShotgun' => 'Close Encounters',
            'medalWeaponSmg' => 'Sub Machinist',
            'medalWeaponSword' => 'Regent'
        );
        return $aTitles[$strKey] ?? "";
    }
}
?>
