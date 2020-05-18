<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//require 'tests/AlphabetTest.php';
require 'src/Automatons/Components/Alphabet/Symbol.php';
require 'src/Automatons/Exceptions/DuplicateItemException.php';
require 'src/Automatons/Set.php';
require 'src/Automatons/Components/Alphabet/Alphabet.php';
require 'src/Automatons/Components/States/State.php';
require 'src/Automatons/Components/States/StateSet.php';
require 'src/Automatons/Components/Transitions/Transition.php';
require 'src/Automatons/Components/Transitions/TransitionSet.php';
//require 'src/Automatons/Components/Transitions/SingleTransition.php';
//require 'src/Automatons/Components/Transitions/SingleTransitionSet.php';
require 'src/Automatons/Automaton.php';
require 'src/Automatons/StateGroup.php';
require 'src/Automatons/StateGroupSet.php';
require 'src/Automatons/StateGroupMap.php';
require 'src/Automatons/Exceptions/InvalidSetException.php';
require 'src/Automatons/StateSetSet.php';
require 'src/Automatons/Utils/Helpers.php';
//require 'src/Automatons/Set.php';

use Autto\Components\Alphabet\Symbol;
use Autto\Components\Alphabet\Alphabet;
use Autto\Components\States\State;
use Autto\Components\States\StateSet;
use Autto\Components\Transitions\Transition;
use Autto\Components\Transitions\TransitionSet;
use Autto\Automaton;
use Autto\Utils\Helpers;

/*$my_str = ["a"=>TRUE, "b"=>FALSE];

//Helpers::replaceStrInArray($my_str, "Mbola", "Makaka");

unset($my_str["a"]);

var_dump(count(array_filter($my_str, function($x) { return !empty($x); })));

var_dump($my_str);

$str = (string) "Mbola ";
var_dump(trim($str));*/

/*$a = new Symbol('a');
$b = new Symbol('b');
$c = new Symbol('c');
$alphabet = new Alphabet;
$alphabet->add($a);
$alphabet->add($b);
$alphabet->add($c);

//$q0 = new State('{q0,q1}');
$q1 = new State('q1');
$q2 = new State('q2');
$q3 = new State('q3');
$q4 = new State('q4');
$q5 = new State('q5');
$q6 = new State('q6');

$etats = new StateSet;
//$etats->add($q0);
!$etats->has($q1, TRUE, TRUE);
!$etats->has($q2, TRUE, TRUE);
!$etats->has($q3, TRUE, TRUE);
!$etats->has($q4, TRUE, TRUE);
!$etats->has($q5, TRUE, TRUE);
!$etats->has($q6, TRUE, TRUE);

$etats_initiaux = new StateSet;
!$etats_initiaux->has($q1, TRUE, TRUE);
!$etats_initiaux->has($q6, TRUE, TRUE);

$cible_1 = new StateSet;
!$cible_1->has($q2, TRUE, TRUE);
!$cible_1->has($q6, TRUE, TRUE);

$cible_2 = new StateSet;
!$cible_2->has($q1, TRUE, TRUE);

$cible_3 = new StateSet;
!$cible_3->has($q5, TRUE, TRUE);

$cible_4 = new StateSet;
!$cible_4->has($q3, TRUE, TRUE);

$cible_5 = new StateSet;
!$cible_5->has($q5, TRUE, TRUE);
!$cible_5->has($q3, TRUE, TRUE);

$cible_6 = new StateSet;
!$cible_6->has($q5, TRUE, TRUE);

$cible_7 = new StateSet;
!$cible_7->has($q4, TRUE, TRUE);


$transition_1 = new Transition($q1, $cible_1, $a);
$transition_2 = new Transition($q2, $cible_2, $a);
$transition_3 = new Transition($q2, $cible_5, $b);
$transition_4 = new Transition($q2, $cible_7, $c);
$transition_5 = new Transition($q3, $cible_6, $c);
$transition_6 = new Transition($q4, $cible_4, $c);
$transition_7 = new Transition($q5, $cible_4, $c);
$transition_8 = new Transition($q6, $cible_6, $c);

$transitions = new TransitionSet;
!$transitions->has($transition_1, TRUE, TRUE);
!$transitions->has($transition_2, TRUE, TRUE);
!$transitions->has($transition_3, TRUE, TRUE);
!$transitions->has($transition_4, TRUE, TRUE);
!$transitions->has($transition_5, TRUE, TRUE);
!$transitions->has($transition_6, TRUE, TRUE);
!$transitions->has($transition_7, TRUE, TRUE);
!$transitions->has($transition_8, TRUE, TRUE);

$etats_finaux = new StateSet;
!$etats_finaux->has($q1, TRUE, TRUE);
!$etats_finaux->has($q3, TRUE, TRUE);
!$etats_finaux->has($q5, TRUE, TRUE);

$automate = new Automaton($etats, $alphabet, $transitions, $etats_initiaux, $etats_finaux);

var_dump($automate->getTransitions()->filterByState(new State('{q0,q1}'))->filterBySymbol(new Symbol('a'))->getItems()); 

echo "Transitions Avant";

foreach($automate->getTransitions() as $item){
    var_dump($item->getFrom());
    var_dump($item->getTo());
    var_dump($item->getOn());
    var_dump("Espace");
}

echo "Fin";
echo "<br>";
echo "Etats finaux";
echo "<br>";
var_dump($automate->getFinalStates());

echo "String";

$str = "";
//var_dump($q2);
$automate->getStr($q3, $str);
var_dump($str);

echo "<br>";

$automate->determinize();

//$automate->minimize();

echo "Transitions Après";

foreach($automate->getTransitions() as $item){
    var_dump($item->getFrom());
    var_dump($item->getTo());
    var_dump($item->getOn());
    var_dump("Espace");
}

echo "Etats";
var_dump($automate->getStates());

echo "Etats initiaux";

var_dump($automate->getInitialStates());

echo "Etats finaux";

var_dump($automate->getFinalStates());

var_dump($automate->compatible("ac"));*/



/*echo "Petits tests";

var_dump($automate->IsDeterministic());

echo "MCA";*/

/*$mca = Helpers::mca(["aa", "aabd","bab", "aab", "bb"]);

?>

<table border="1">
    <tr>
        <th>MCA</th>
    </tr>
    <tr>
        <td>Transitions</td>
    </tr>
    <tr>
        <td><?php 
            foreach($mca->getTransitions() as $trans){
                var_dump($trans->getFrom(), $trans->getTo(), $trans->getOn(), "Saut");
            }
            ?></td>
    </tr>
    <tr>
        <td>Etats</td>
    </tr>
    <tr>
        <td><?php var_dump($mca->getStates()->getItems()); ?></td>
    </tr>
    <tr>
        <td>Etats Initiaux</td>
    </tr>
    <tr>
        <td><?php var_dump($mca->getInitialStates()->getItems()); ?></td>
    </tr>
    <tr>
        <td>Etats Finaux</td>
    </tr>
    <tr>
        <td><?php var_dump($mca->getFinalStates()->getItems()); ?></td>
    </tr>
    <tr>
        <td>Alphabet</td>
    </tr>
    <tr>
        <td><?php var_dump($mca->getAlphabet()); ?></td>
    </tr>
</table>
<?php*/
/*
echo "IsInPartition";

$state_set_0 = new StateSet;
$state_set_0->add(new State("eps"));

$state_set_1 = new StateSet;
//$state_set_1->add(new State("a"));
$state_set_1->add(new State(Symbol::EPSILON));

$state_set_2 = new StateSet;
$state_set_2->add(new State("a"));
//$state_set_2->add(new State("bab"));

$state_set_3 = new StateSet;
$state_set_3->add(new State("ab"));

$state_set_4 = new StateSet;
$state_set_4->add(new State("b"));

$state_set_5 = new StateSet;
$state_set_5->add(new State("ba"));
$state_set_5->add(new State("ab"));

$state_set_6 = new StateSet;
$state_set_6->add(new State("bab"));
$state_set_6->add(new State("abc"));

$state_set_7 = new StateSet;
//$state_set_7->add(new State("abc"));

$partition = [ $state_set_2, $state_set_3, $state_set_4, $state_set_5, $state_set_6, $state_set_7, $state_set_1];*/

/*$part = $mca->getPartitionPositive();

foreach($part as $p){
    var_dump($p->getItems());
}*/



/*$pca_1 = clone($mca);
$pca_1 = $pca_1->derive($mca->getPartitionPositive());

echo "États";

var_dump($pca_1->getStates());

echo "États initiaux";

var_dump($pca_1->getInitialStates());

echo "États finaux";

var_dump($pca_1->getFinalStates());

echo "Transitions";

foreach($pca_1->getTransitions() as $trans){
    var_dump("From : ",$trans->getFrom());
    foreach($trans->getTo() as $to){
        var_dump("To : ", $to);
    }
    var_dump("On : ",$trans->getOn());
}

var_dump(Helpers::isInPartition($partition, $state_set_6));

foreach($partition as $part){
    var_dump($part->getItems());
}

echo "Remove Double";

Helpers::removeDoubles($partition);

foreach($partition as $part){
    var_dump($part->getItems());
}*/

/*echo "Partition Positive";

$partition_pos = $mca->getPartitionPositive();

foreach($partition_pos as $part){
    var_dump($part->getItems());
}

echo "PTA";

$pta = $mca->derive($mca->getPartitionPositive());

$tmp_pta = clone($pta);

$tmp_pta->determinize();

?>

<table border="1">
    <tr>
        <th>PTA</th>
        <th>PTA deterministe</th>
    </tr>
    <tr>
        <td>Transitions</td>
        <td>Transitions</td>
    </tr>
    <tr>
        <td><?php 
            foreach($pta->getTransitions() as $trans){
                var_dump($trans->getFrom(), $trans->getTo(), $trans->getOn(), "Saut");
            }
            ?>
        </td>
        <td><?php 
            foreach($tmp_pta->getTransitions() as $trans){
                var_dump($trans->getFrom(), $trans->getTo(), $trans->getOn(), "Saut");
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>Etats</td>
        <td>Etats</td>
    </tr>
    <tr>
        <td><?php var_dump($pta->getStates()->getItems()); ?></td>
        <td><?php var_dump($tmp_pta->getStates()->getItems()); ?></td>
    </tr>
    <tr>
        <td>Etats Initiaux</td>
        <td>Etats Initiaux</td>
    </tr>
    <tr>
        <td><?php var_dump($pta->getInitialStates()->getItems()); ?></td>
        <td><?php var_dump($tmp_pta->getInitialStates()->getItems()); ?></td>
    </tr>
    <tr>
        <td>Etats Finaux</td>
        <td>Etats Finaux</td>
    </tr>
    <tr>
        <td><?php var_dump($pta->getFinalStates()->getItems()); ?></td>
        <td><?php var_dump($tmp_pta->getFinalStates()->getItems()); ?></td>
    </tr>
    <tr>
        <td>Alphabet</td>
        <td>Alphabet</td>
    </tr>
    <tr>
        <td><?php var_dump($pta->getAlphabet()); ?></td>
        <td><?php var_dump($tmp_pta->getAlphabet()); ?></td>
    </tr>
    <tr>
        <td><?php var_dump($pta->IsDeterministic()); ?></td>
        <td><?php var_dump($tmp_pta->IsDeterministic()); ?></td>
    </tr>
</table>

<?php

var_dump($pta->compatible("bab"));*/

echo "RPNI";
echo "<br>";

$rpni = Helpers::rpni(["a", "ab", "bab"], ["aba"]);//I+ = "Velo", "Moto", "Couture", "Voirie", "Voicure"|"aaa", "aaba", "ababa", "bb", "bbaaa" OR "a", "ab","bab", "abc"/I-="Voiture", "Coirie"|"aa", "ab", "aaaa", "ba"

echo "Transitions RPNI";

foreach($rpni->getTransitions() as $trans){
    var_dump($trans->getFrom());
    foreach($trans->getTo() as $to){
        var_dump($to);
    }
    var_dump($trans->getOn(), "Fin");
}

echo "Etats RPNI";

var_dump($rpni->getStates());

echo "Etats initiaux RPNI";

var_dump($rpni->getInitialStates());

echo "Etats finaux RPNI";

var_dump($rpni->getFinalStates());

var_dump($rpni->IsDeterministic());

var_dump($rpni->compatible("aba"));