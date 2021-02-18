<?php
    if(isset($_GET['delete'])){
        echo $video->deleteVideo($video->getdetails('', $_GET['delete'], 'videoFileName'), $video->getdetails('', $_GET['delete'], 'extension'));

        header('Location: ' . $website_url . '/admin?a=videos');
    }
?>
<h1>Main</h1><hr />
<div class="row">
    <div class="col-md-4 w-50">
        <h2>New users per month</h2><br />
        <canvas id="newUsers" width="400" height="250"></canvas>
        <hr />
        <h2>Total users registered</h2>
        <?php echo $user->getCurrentUserCount(); ?>
    </div>
    <div class="col-md-4 w-50">
        <h2>New videos per month</h2><br />
        <canvas id="newVideos" width="400" height="250"></canvas>
        <hr />
        <h2>Total videos uploaded</h2>
        <table class="w-50">
            <tr>
                <td>Total:</td>
                <td><?php echo $video->getCurrentVideoCount(['all']); ?></td>
            </tr>
            <tr>
                <td>Excluding deleted:</td>
                <td><?php echo $video->getCurrentVideoCount(['noDeleted']); ?></td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <h2>Recent reports</h2><hr />
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <td class="align-text-top w-50">
                        <table class="w-100">
                            <tr>
                                <td style="border-bottom: 1px solid;">Report reason</td>
                                <td style="border-bottom: 1px solid;">Reported by</td>
                                <td style="border-bottom: 1px solid;">Reported party</td>
                                <td style="border-bottom: 1px solid;">Date</td>
                            </tr>
                            <?php echo $warning->getRecentReports(); ?>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script>
    var ctx = document.getElementById('newUsers').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels : ["January","February","March","April","May","June","July","August","September","October","November","December"],
            datasets: [{
                data: <?php echo $user->newUsersPerMonth(); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }]
            },
            legend: {
                display: false
                }
        }
    });
</script>
<script>
    var ctx = document.getElementById('newVideos').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels : ["January","February","March","April","May","June","July","August","September","October","November","December"],
            datasets: [{
                data: <?php echo $video->newVideosPerMonth(); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }]
            },
            legend: {
                display: false
                }
        }
    });
</script>