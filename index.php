<?php
//Require autoload file
require_once('vendor/autoload.php');

session_start();

ini_set('display_errors', true);
error_reporting(E_ALL);

//validate file
require_once('model/validate.php');

//Create an instance of the Base class
$f3 = Base::instance();

/**
 * set arrays
 */
//states
$f3->set('states', array('Alabama','Alaska','Arizona','Arkansas','California',
    'Colorado','Connecticut','Delaware','District of Columbia','Florida','Georgia',
    'Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana',
    'Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri',
    'Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York',
    'North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island',
    'South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington',
    'West Virginia','Wisconsin','Wyoming'));

//in-door
$f3->set("indoorInterests", array('tv', 'puzzles', 'movies', 'reading', 'cooking', 'playing cards', 'board games', 'video games'));

//out-door
$f3->set("outdoorInterests", array('hiking', 'walking', 'biking', 'climbing', 'swimming', 'collecting'));

//define a default route/home page
$f3->route('GET /', function () {
    $view = new Template();
    echo $view->render('views/home.html');
});

//Route to information form1 - personal
$f3->route('GET|POST /personal', function ($f3)
{
    if(!empty($_POST))
    {
        //Get data
        $first = $_POST['first'];
        $last = $_POST['last'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];
        $membership = $_POST['membership'];

        //Add to hive
        $f3->set('first', $first);
        $f3->set('last', $last);
        $f3->set('age', $age);
        $f3->set('gender', $gender);
        $f3->set('phone', $phone);
        $f3->set('membership', $membership);

        //Validate form 1-personal
        if (form1())
        {
            //Session
            $_SESSION['first'] = $first;
            $_SESSION['last'] = $last;
            $_SESSION['age'] = $age;
            $_SESSION['phone'] = $phone;
            if (empty($gender)) {
                $_SESSION['gender'] = "No gender selected";
            } else {
                $_SESSION['gender'] = $gender;
            }

            //get data from classes
            if($membership === "premium")
            {
                $member = new PremiumMember($first, $last, $age, $gender, $phone);
            }
            else
            {
                $member = new Member($first, $last, $age, $gender, $phone);
            }
            $_SESSION['member'] = $member;

            //reroute to profile
            $f3->reroute('/profile');
        }
    }
    $view = new Template();
    echo $view->render('views/personal.html');
});

//Route to information form2 - profile
$f3->route('GET|POST /profile', function ($f3)
{
    if(!empty($_POST))
    {
        //Get data
        $email = $_POST['email'];
        $state = $_POST['state'];
        $bio = $_POST['bio'];
        $seeking = $_POST['seeking'];
        //Add data to hive
        $f3->set('email', $email);
        $f3->set('state', $state);
        $f3->set('bio', $bio);
        $f3->set('seeking', $seeking);

        //validate form 2
        if (form2()) {
            //Write data to Session
            $_SESSION['email'] = $email;
            $_SESSION['state'] = $state;
            if (empty($bio)) {
                $_SESSION['bio'] = "No biography";
            }
            else {
                $_SESSION['bio'] = $bio;
            }

            if (empty($seeking)) {
                $_SESSION['seeking'] = "Not seeking any";
            }
            else {
                $_SESSION['seeking'] = $seeking;
            }

            $member = $_SESSION['member'];
            $member->setEmail($email);
            $member->setState($state);
            $member->setBio($bio);
            $member->setSeeking($seeking);
            $_SESSION['member'] = $member;

            if($member instanceof PremiumMember)
            {
                $f3->reroute('/interests');
            }

            //reroute to summary
            $f3->reroute('/summary');
        }
    }
    $view = new Template();
    echo $view->render('views/profile.html');
});

//Route to interest
$f3->route('GET|POST /interests', function ($f3)
{
    if(!empty($_POST))
    {
        //Get data
        $indoor = $_POST['indoor'];
        $outdoor = $_POST['outdoor'];

        //Add to hive
        $f3->set('indoor', $indoor);
        $f3->set('outdoor', $outdoor);

        //interests validation
        if (validInterest()) {
            //Session
            //indoor
            if (empty($indoor)) {
                $_SESSION['indoor'] = ["no indoor interests"];
            }
            else {
                $_SESSION['indoor'] = $indoor;
            }
            //outdoor
            if (empty($outdoor)) {
                $_SESSION['outdoor'] = ["no outdoor interests"];
            }
            else {
                $_SESSION['outdoor'] = $outdoor;
            }

            $_SESSION['member']->setInDoorInterests($indoor);
            $_SESSION['member']->setOutDoorInterests($outdoor);

            //reroute to summary
            $f3->reroute('/summary');
        }
    }
    $view = new Template();
    echo $view->render('views/interest.html');
});

//Route to summary
$f3->route('GET|POST /summary', function ()
{
    $view = new Template();
    echo $view->render('views/summary.html');
});
//Run fat-free
$f3->run();
?>