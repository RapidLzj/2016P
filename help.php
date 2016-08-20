<?php
$pagetitle = 'Help';
require 'conn.php';
require 'const.php';

?>

<?php require 'head.php'; ?>

<p class="title">Help for SSSOL</p>

<p>This system is used to write and print log for SAGE Sky Survey Observation.</p>

<h3>Basic</h3>

<h5>Run</h5>
<p>Some continually planned observation nights are called a run.
    Usually we name a run by its year, month and telescope initial.
    For example, a run using Bok on Jan 2016 is called 201601_B.
    If there are more than 1 run in a month, we put A, B, and etc to replace the underline.
</p>

<p>Another naming method is to use 24 half month code, as we do for asteroids.</p>

<p>If a run starts at the end of last month, and extends into next month,
    we will name it by its start date.</p>

<h5>Observing Night</h5>
<p>We have two ways to name an observing night, one is the local date of start,
    the other is use Julian day number.</p>

<p>When use human readable data format, we use local date, but not UTC date.
    We use the day when the night start, whatever real starting hour is.
    We use <code>yyyymmdd</code> format, 2 digits for month and day.</p>

<p>When use Julian day, we use <code>Jxxxx</code> format, xxxx means the last 4 digits of MJD.
    We use MJD of local 18:00 at starting day, when ever the real observation starts.
    And when we talk about local time, we use local official time,
    what ever timezone the telescope really locates,
    and discard daylight saving shift.</p>

<p>Date format and JD format are all provided by program.</p>

<h3>Log</h3>

<h5>Start log</h5>
<p>Click on &#x1f4dd; tag at right of date line to write log.</p>

<h5>Weather</h5>
<p>For general weather condition, choose one which describe the worst situation of the night.
    Items with a leading &cross; mean operators must shutdown and pause observation.</p>

<p>Weather description will record detailed weather parameters.</p>

<h5>Status</h5>
<p>Status means a conclusion of this night.
    If the whole night is used without problem, check <code>obsed</code>.
    If the night is shared with other proposal, we still take the night as a whole night.
    But when bad weather or device problem makes time lost, do not check <code>obsed</code>,
    instead of checking <code>weather</code> or <code>device</code>.</p>

<p>Device problem means we have to pause working and fix it. If device is only unstable
    and make some bad images, we need only log bad numbers, the night is still whole.</p>

<p><code>Other</code> means inserted by other observations, for example GRB.
    And <code>else</code> stands for any reason not listed above.</p>

<p>Once the night is canceled or rescheduled, check <code>cancel</code> and <code>nowork</code>.</p>

<h5>Log items</h5>
<p>For log items, time is important column, please choose the start time of event or operation.
    If you want to delete a line, select &cross; at hour drop-down box.
    File number column can be empty, if the operation is not observation.</p>

<p>These event should be logged, normal observation, shifting to a new list, bias and flat,
    focusing and focus parameters, pause and resume of observation and reason for it,
    bad weather or device problem, inserted works, dewar filling,
    and any other event need to let readers known.</p>

<p>Bad image should also be recorded, in item note or night note.</p>

<h5>Other parts</h5>
<p>Plan is the original plan of this night, if no special purpose, just write normal.</p>

<p>Result is corresponding to plan.</p>

<p>Note is used for special cases in this night need to record. Or anything else to cells above.</p>

<h5>Save and Submit</h5>
<p>In writing log, you can click <code>save</code> button to save at anytime.
    After saving, system will provide 10 extra empty log items.
    Saving log will not end work and will not submit.</p>

<p>After observation, click <code>submit</code> button to preview the whole log.
    If all status is unchecked or operator names is missing, you cannot submit.
    At preview interface, you can go back to editing form.</p>

<p>Log form before submitting cannot be read by others, only operators can edit it.</p>

<p>If you want to change submitted log, it is allowed, before the manager accept the log.
    But your last submit time will be recorded and shown on log.</p>

<?php require 'foot.php'; ?>
<?php require 'conx.php'; ?>
