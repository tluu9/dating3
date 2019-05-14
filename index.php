<?php
/**
Trang Luu
20 Apil 2019
Dating Website/ HTML Home page
*/
session_start();

//Turn on error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

//require the autoload file autoload.php
require_once('vendor/autoload.php');
require_once('model/validate.php');

//Create an instance of the Base class/ instantiate Fat-Free
$f3 = Base::instance();

//Turn on Fat-free error reporting/Debugging
$f3->set('DEBUG',3);

//Genders array
$f3-> set('genders', array('Male', 'Female'));

//State
$f3->set('states', array('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
    'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho','District of Columbia',
    'Iowa','Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana ', 'Maine',
    'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri',
    'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico',
    'New York', 'North Carolina', 'North Dakota', 'Ohio ', 'Oklahoma', 'Oregon ',
    'Puerto Rico','Pennsylvania', 'Rhode Island ', 'South Carolina', 'South Dakota ', 'Tennessee',
    'Texas ', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin ',
    'Wyoming',
));

//interest
$f3->set('indoor1', array('TV', 'Movies', 'Cooking', 'Card games'));
$f3->set('indoor2', array('Puzzles', 'Reading', 'Contests', 'Video games'));
$f3->set('outdoor1', array('Hiking', 'Running', 'Swimming', 'Battling'));
$f3->set('outdoor2', array('Training', 'Climbing'));

//Define a default route
$f3 ->route('GET /', function() {
    $view = new Template();
    echo $view ->render('views/home.html');
});
//form 1-personal
$f3 ->route('POST /personal', function($f3) {
    if(!empty($_POST))
    {
        //get data
        $lname = $_POST['lname'];
        $fname = $_POST['fname'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];
        $membership = $_POST['membership'];

        //and add to hive
        $f3->set('lname', $lname);
        $f3->set('fname', $fname);
        $f3->set('age', $age);
        $f3->set('gender', $gender);
        $f3->set('phone', $phone);
        $f3->set('membership', $membership);


        if(form1())
        {
            $_SESSION['lname'] = $lname;
            $_SESSION['fname'] = $fname;
            $_SESSION['age'] = $age;
            $_SESSION['gender'] = $gender;

            if (!empty($membership)) {
                $newMember = new PremiumMember($fname, $lname, $age, $gender, $phone);
                $_SESSION['member'] = $newMember;
                }
            else
                {
                $newMember = new Member($fname, $lname, $age, $gender, $phone);
                $_SESSION['member'] = $newMember;
                }

            $f3->reroute('/profile');
        }
    }
    $view = new Template();
    echo $view ->render('views/personal.html');
});

//form 2-profile
$f3 ->route('GET|POST /profile', function($f3) {
    if(!empty($_POST))
    {
        $email = $_POST['email'];
        $state = $_POST['state'];
        $seeking = $_POST['seeking'];
        $bio = $_POST['bio'];

        $f3->set('email', $email);
        $f3->set('state', $state);
        $f3->set('seeking', $seeking);
        $f3->set('bio', $bio);

        if(form2())
        {
            $_SESSION['member']->setEmail($email);
            $_SESSION['member']->setState($state);
            $_SESSION['member']->setSeeking($seeking);
            $_SESSION['member']->setBio($bio);

            if ($_SESSION['member'] instanceof PremiumMember)
                {
                $f3->reroute('/interests');
                }
            else
                {
                $f3->reroute('/summary');
                }
        }
    }
    $view = new Template();
    echo $view ->render('views/profile.html');
});
//form 3 interest
$f3 ->route('GET|POST /interest', function($f3) {
    if(!empty($_POST))
    {
        $indoor = $_POST['indoor'];
        $outdoor = $_POST['outdoor'];

        $f3->set('indoor', $indoor);
        $f3->set('outdoor', $outdoor);

        if(form3())
        {
//            $_SESSION['indoor'] = $indoor;
//            $_SESSION['outdoor'] = $outdoor;

            $_SESSION['member']->setInDoorInterests($indoor);
            $_SESSION['member']->setOutDoorInterests($outdoor);

            $f3->reroute('/summary');
        }
    }
    $view = new Template();
    echo $view ->render('views/interest.html');
});

//Define the route to the summary
$f3 ->route('GET|POST /summary', function() {
    $view = new Template();
    echo $view ->render('views/summary.html');
});

//Run fat free
$f3 ->run();