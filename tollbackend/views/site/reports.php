<div ng-app="tableCSV" ng-controller="sampleController">
<div class="header">
    <div class="container" style="padding-top:10px;">
        <!-- Header nav row start -->
        <div class="row">
            <nav class="navbar navbar-inverse navbar-custom navbar-inverse-custom">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand navbar-brand-custom" href="#"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/logo.png" width="119" height="61" alt=""/></a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse navbar-collapse-custom" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li class=""><a href="<?php echo Yii::$app->request->baseUrl; ?>">Home <span class="sr-only">(current)</span></a></li>
                            <li><a href="">Reports</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <a class="navbar-brand navbar-brand-custom" style="float:right;" href="#"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/nha-logo.png" width="91" height="60" alt=""/></a>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
        </div>
        <!-- Header nav row end -->


    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 p0">
            Friday, Feb 19, 2016
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 p0 tr">
            <div class="disinblo">
                <img src="images/user.png" width="48" height="47" alt="" class="login-user-icon"/>
            </div>
            <div class="login-user-text disinblo">
                <div align="left"><?php echo Yii::$app->user->identity->toll_employee_id;?></div>
                <div align="left"><a href="<?php echo Yii::$app->request->baseUrl; ?>/site/logout">Logout</a></div>
            </div>
        </div>

    </div>
</div>
<!-- content row start -->
<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rightPannel">
            <div class="rightPanBox">
                <div class="header2">
                    <div class="col-lg-3 col-md-3 col-sm-3 hidden-xs">
                        <div style="margin:26px 0 0 0; font-family:OMUPro-Light; color:#FFFFFF;">
                            <div style="font-size:40px;">1245</div><div style="font-size:22px; padding-left:8px;">tollered</div>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12" align="right">
                        <form>
                            <div id="dirBut" class="direction-but"></div>
                            <div id="state" style="display:block">
                                <div style="padding-top:40px;">
                                    
                                    <select class="search-by" name="state_by">
                                        <option style="border:0;" value="">Select State</option>
                                        <option style="border:0;" value="Andhra Pradesh">Andhra Pradesh</option>
                                        <option style="border:0;" value="Telangana">Telangana</option>
                                        <option style="border:0;">Karnataka</option>
                                        <option style="border:0;">Maharashtra</option>
                                        <option style="border:0;">Madhya Pradesh</option>
                                    </select>
                                </div>
                            </div>
                            <div id="direction" style="display:none">
                                <div><input type="text" class="starting-point" placeholder="choose starting point"></div>
                                <div><input type="text" class="disti-point" placeholder="choose destination"></div>
                                <div><button class="point-point-but">Find</button></div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="content-area">
                    <div class="pad-left-right" align="right">

                            <span>
                            	<select class="list-field">
                                    <option style="border:0;">Till date on this month</option>
                                    <option style="border:0;">July</option>
                                    <option style="border:0;">Aug</option>
                                    <option style="border:0;">Sep</option>
                                    <option style="border:0;">Oct</option>
                                    <option style="border:0;">Nov</option>
                                </select>
                            </span>
                          <span>
                       	  <select class="list-field">
                                    <option style="border:0;">Daily</option>
                                    <option style="border:0;">Monthly</option>                                   
                              </select>
                          </span>
                            <span>
                            	<a ng-click="exelDownload()"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/excel.png" width="23" height="23" alt=""/></a>
                                
                            </span>
                    </div>
                    <hr style="width:96%; margin:10px auto;">
                    <div align="center" id="report_screen">
                        <table  width="96%" border="0" cellspacing="0" class="content-table">
                            <thead>
                            <tr class="date-row">
                                <td>Date</td>
                                <td>&nbsp;</td>
                                <td>Bike</td>
                                <td>Car/Jeep/Van</td>
                                <td>LCV</td>
                                <td>Buus/Truck</td>
                                <td>Up to 3 Axle</td>
                                <td>4 to 6 Axle</td>
                                <td>HCM/EME</td>
                                <td>7 / more Axle</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($history as $value){ ?>
                                <tr class="date-row">
                                    <td rowspan="2" class="border-dark"><?php echo date('d/m/Y',strtotime($value->date)) ?></td>
                                    <td class="border-light">Amount</td>
                                    <?php foreach($vehical_types as $type){ ?>
                                        <td class="border-light"><?php echo is_null($value["amount_".$type->vechical_types_id])? 0: $value["amount_".$type->vechical_types_id]; ?></td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="border-dark border-light-left">Traffic</td>
                                    <?php foreach($vehical_types as $type){ ?>
                                        <td class="border-light"><?php echo is_null($value["counter_".$type->vechical_types_id]) ? 0 : $value["counter_".$type->vechical_types_id]; ?></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <div align="right" class="pad-left-right">Average Daily collection from 1st Mar to till date is    -    <strong>1,43,245</strong></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- content row end -->
<script type="application/javascript">
    var base_url = '<?php echo Yii::$app->request->baseUrl; ?>';
    var pathInfo = '<?php echo Yii::$app->request->pathInfo; ?>';
    var toll_user_id = '<?php echo Yii::$app->user->identity->toll_employee_id;?>';
    
</script>