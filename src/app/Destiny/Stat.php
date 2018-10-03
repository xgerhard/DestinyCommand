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
            'weaponKillsMelee' => 'melee kills',
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
            /* Gambit stats */
            'invasions' => 'invasions',
            'invasionKills' => 'invasion kills',
            'invaderKills' => 'invader kills',
            'invaderDeaths' => 'invader deaths',
            'primevalKills' => 'primeval kills',
            'blockerKills' => 'blocker kills',
            'mobKills' => 'mob kills',
            'highValueKills' => 'high value enemies kills',
            'motesPickedUp' => 'motes picked up',
            'motesDeposited' => 'motes deposited',
            'motesDenied' => 'motes denied',
            'motesDegraded' => 'motes degraded',
            'motesLost' => 'motes lost',
            'bankOverage' => 'bank overage',
            'smallBlockersSent' => 'small blockers sent',
            'mediumBlockersSent' => 'medium blockers sent',
            'largeBlockersSent' => 'large blockers sent',
            'primevalDamage' => 'primeval damage',
            'primevalHealing' => 'primeval healing',
            'roundsPlayed' => 'rounds played',
            'roundsWon' => 'rounds won',
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
            'medalWeaponSword' => 'Regent',
            'medalMatchNeverTrailed' => 'Never In Doubt',
            'medalMatchComeback' => 'From the Jaws of Defeat',
            'medalAbilityDawnbladeSlam' => 'Falling Star',
            'medalAbilityDawnbladeAerial' => 'Defying Gravity',
            'medalAbilityVoidwalkerVortex' => 'Singularity',
            'medalAbilityVoidwalkerDistance' => 'From Downtown',
            'medalAbilityStormcallerLandfall' => 'Thunderstruck',
            'medalAbilityFlowwalkerQuick' => 'Lightning Strike',
            'medalAbilityNightstalkerTetherQuick' => 'Entangled',
            'medalAbilityNightstalkerLongRange' => 'Longbow',
            'medalAbilitySentinelWard' => 'Perfect Guard',
            'medalAbilitySentinelCombo' => 'Flying Fortress',
            'medalAbilityJuggernautSlam' => 'Absolute Force',
            'medalAbilityJuggernautCombo' => 'Striker Special',
            'medalAbilitySunbreakerLongRange' => 'Pitch Perfect',
            'medalAbilitySunbreakerMulti' => 'Everything Looks Like a Nail',
            'medalCountdownDefense' => 'Counter Attack',
            'medalCountdownDetonated' => 'Pyrotechnics',
            'medalCountdownDefusedMulti' => 'Bombs? What Bombs?',
            'medalCountdownDefusedLastStand' => 'Last Stand',
            'medalCountdownPerfect' => 'Perfect Game',
            'medalSurvivalWinLastStand' => 'Lone Gun',
            'medalSurvivalQuickWipe' => 'Minute to Win It',
            'medalSurvivalKnockout' => 'Undertaker',
            'medalSurvivalComeback' => 'According to Plan',
            'medalSurvivalTeamUndefeated' => 'Untouchable',
            'medalControlPerimeterKill' => 'Reclaimer',
            'medalControlAdvantageStreak' => 'Dominant Advantage',
            'medalControlPowerPlayWipe' => 'Power Overwhelming',
            'medalSupremacyFirstCrest' => 'First Secure',
            'medalSupremacyRecoverStreak' => 'Steadfast Ally',
            'medalSupremacyCrestCreditStreak' => 'Crestfallen',
            'medalSupremacyPerfectSecureRate' => 'A Crown of Crests',
            'medalMayhemFirstSuper' => 'Light \'Em Up',
            'medalMayhemGrenadeStreak' => 'Fire in the Hole!',
            'medalMayhemMeleeStreak' => 'Punch and Pie',
            'medalMayhemCastStreak' => 'Superstar',
            'medalMayhemCastMulti' => 'By Our Powers Combined',
            'medalMayhemKillStreak' => 'Total Mayhem',
            'medalCrimsonWeaponCombo' => 'Polyarmory',
            'medalCrimsonRevengeMulti' => 'Third Wheel',
            'medalCrimsonApartMulti' => 'Broken Up',
            'medalCrimsonSuddenDeath' => 'Heartbreaker',
            'medalRumbleDefeatAllClasses' => 'Best in Class',
            'medalRumbleUnassistedStreak' => 'Assassin',
            'medalRumbleStealStreak' => 'Pickpocket',
            'medalRumbleTop3' => 'Podium Finish',
            'medalRumbleDefeatAllPlayers' => 'Round Robin',
            'medalRumbleBetterThanAllCombined' => 'The Sum of All Tears',
            'medalSlayer' => 'Slayer',
            'medalStreak6x' => 'Reaper',
            'medalStreak7x' => 'Seventh Column',
            'medalShowdownMostKills' => 'Local Maxima',
            'medalShowdownAmmoStreak' => 'Denial of Service',
            'medalShowdownRetakeLead' => 'Clawing Back',
            'medalShowdownFullTeamSurvival' => 'When the Dust Clears',
            'medalShowdownForceFinalRound' => 'We\'re Not Done Yet',
            'medalShowdownUndefeated' => 'Invincible',
            'allMedalsEarned' => 'Total medals',
            /* Gambit only medals */
            'medals_pvecomp_medal_invader_kill_four' => 'Army of One',
            'medals_pvecomp_medal_massacre' => 'Massacre',
            'medals_pvecomp_medal_no_escape' => 'No Escape',
            'medals_pvecomp_medal_blockbuster' => 'Blockbuster',
            'medals_pvecomp_medal_tags_denied_15' => 'Mote Have Been',
            //'medals_pvecomp_medal_bank_kill' => '',
            'medals_pvecomp_medal_denied' => 'Denied',
            'medals_pvecomp_medal_invasion_shutdown' => 'Not on My Watch',
            'medals_pvecomp_medal_locksmith' => 'Locksmith',
            'medals_pvecomp_medal_block_party' => 'Block Party',
            'medals_pvecomp_medal_never_say_die' => 'Never Say Die',
            'medals_pvecomp_medal_half_banked' => 'Half-Banked',
            'medals_pvecomp_medal_everyone_invaded' => 'Taking Turns',
            'medals_pvecomp_medal_killmonger' => 'Killmonger',
            'medals_pvecomp_medal_thrillmonger' => 'Thrillmonger',
            'medals_pvecomp_medal_overkillmonger' => 'Overkillmonger',
            'medals_pvecomp_medal_value_hunter' => 'Value Hunter',
            'medals_pvecomp_medal_first_to_block' => 'First to Block',
            'medals_pvecomp_medal_fast_fill' => 'Fast Fill',
            'medals_pvecomp_medal_big_game_hunter' => 'Big Game Hunter',
            //'medals_pvecomp_medal_kill_after_invasion' => '',
            'medals_pvecomp_medal_revenge' => 'Payback',
            'medals_pvecomp_medal_revenge_same_invasion' => 'Rapid Payback',
            'medals_pvecomp_medal_fist_of_havoc_multikill' => 'Titan Smash',
            'medals_pvecomp_medal_meteor_strike_multikill' => 'A Titan Can Fly',
            'medals_pvecomp_medal_ward_of_dawn_blocking' => 'Impenetrable Ward of Dawn',
            'medals_pvecomp_medal_void_shield_multikill' => 'Captain of the Void',
            'medals_pvecomp_medal_thermal_hammer_multikill' => 'A Time for Hammers',
            'medals_pvecomp_medal_thermal_maul_multikill' => 'Burning Path',
            'medals_pvecomp_medal_arc_staff_multikill' => 'Lightning Rod',
            'medals_pvecomp_medal_golden_gun_multikill' => 'Fire Slinger',
            'medals_pvecomp_medal_thermal_knives_multikill' => 'Fan of Knives',
            'medals_pvecomp_medal_void_blade_multikill' => 'Spectral Surgeon',
            'medals_pvecomp_medal_void_bow_multikill' => 'Ensnarement',
            'medals_pvecomp_medal_arc_lightning_multikill' => 'Ride Lightning',
            'medals_pvecomp_medal_arc_beam_multikill' => 'Chaos Incarnate',
            'medals_pvecomp_medal_nova_bomb_multikill' => 'Void-Based Demolition',
            'medals_pvecomp_medal_nova_pulse_multikill' => 'Void Unleashed',
            'medals_pvecomp_medal_thermal_sword_multikill' => 'Rain of Fire',
            'Medals_pvecomp_medal_thermal_sword_healing_multikill' => 'Dug In'
        );
        return $aTitles[$strKey] ?? "";
    }
}
?>
