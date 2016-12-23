<?php
require __DIR__ . '/lib/vendor/autoload.php';


$data = file_get_contents('https://www.google.com/calendar/ical/hellofutu.re_jqt60gbfg8immtfmq98jp5boeo%40group.calendar.google.com/public/basic.ics');

$calendar = new \HelloFuture\Schedule\Calendar($data);
$unbookedReporter = $calendar->getUnbookedReporter();
$clientReporter = $calendar->getClientReporter();

$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
$twig = new Twig_Environment($loader);

?>

<html>
    <head>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
              integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    </head>
    <body>
        <div class="container">
            <?php
            echo $twig->render('client.twig', [
                'title' => 'Client by a-z',
                'clients' => $clientReporter->getClients()
            ]);

            echo $twig->render('singleRow.twig', [
                'title' => 'Total hours booked',
                'content' => $clientReporter->getTotalHours()
            ]);


            echo $twig->render('singleRow.twig', [
                'title' => 'Total hours unbooked',
                'content' => $unbookedReporter->getTotalHours()
            ]);

            echo $twig->render('client.twig', [
                'title' => 'Client by hours',
                'clients' => $clientReporter->getClientsByHours()
            ]);


            echo $twig->render('unbooked.twig', [
                'title' => 'Unbooked ranges',
                'ranges' => $unbookedReporter->getRanges()
            ]);

            ?>

    </body>
</html>

