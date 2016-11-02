<?php use yii\helpers\ArrayHelper; ?>
<div ng-app="Tollr" ng-controller="Consess">
    <div class="header">
        <div class="container" style="padding-top:10px;">
            <!-- Header nav row start -->
            <div class="row">
                <nav class="navbar navbar-inverse navbar-custom navbar-inverse-custom">
                    <div class="container-fluid">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
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
                                <li class=""><a href="<?php echo Yii::$app->request->baseUrl; ?>">Home <span
                                            class="sr-only">(current)</span></a></li>
                                <li><a href="<?php echo Yii::$app->request->baseUrl; ?>/site/reports">Reports</a></li>
                            </ul>
                            <ul class="nav navbar-nav navbar-right">
                                <a class="navbar-brand navbar-brand-custom"  href="#"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/nha-logo.png" width="91" height="60" alt=""/></a>
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
            <div class="col-lg-6 col-md-6 col-sm-6 p0" ng-controller="Ctrl2">
                <span class="date" my-current-time="dateformat"
                      style="color:#ff9600; font-weight: 600;font-family: OMUPro-Light"></span> </br>
                <span class="time" my-current-time="format" style="font-size: 25px; padding-left: 17px;"></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 p0 tr" style="text-align: right;">
                <div class="disinblo">
                    <img width="48" height="47" class="login-user-icon" alt="" src="<?php echo Yii::$app->request->baseUrl; ?>/images/user.png">
                </div>
                <div class="login-user-text disinblo" style="float:right;">
                    <div align="left" style="color:#ff9600; font-weight: 600;font-family: OMUPro-Light"><?php echo Yii::$app->user->identity->toll_employee_id;?></div>
                    <div align="left" style="padding-left: 10px;"><a
                            href="<?php echo Yii::$app->request->baseUrl; ?>/site/logout">Logout</a></div>
                </div>
            </div>

        </div>
    </div>
    <!-- content row start -->
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 rightPannel">
                <div class="rightPanBox">
                    <?php if (!empty($toll_details)) { ?>
                        <div class="header2">
                            <div class="col-lg-12">
                                <div style="margin:22px 0 0 0; font-family:OMUPro-Light; color:#FFFFFF;">
                                    <div style="font-size: 24px; line-height: 34px;"><?php echo $toll_details->toll_name;  ?>
                                    </div>
                                    <div style="font-size: 20px; line-height: 30px;"><?php echo $toll_details->toll_location; ?>
                                    </div>
                                    <!--<div style="font-size: 16px; line-height: 26px;">Tollable Length : 79.360 Km(s)</div>-->
                                </div>
                            </div>
                        </div>

                    <?php } else { ?>
                        <div class="header2">
                            <div class="col-lg-3 col-md-4 col-sm-4 text-right">
                                <div style="margin:35px 0 0 0; font-family:OMUPro-Light; color:#FFFFFF;">
                                    <div style="font-size: 26px; line-height: 38px;">Total Collection</div>
                                    <div style="font-size: 18px; line-height: 28px;">{{counter.sum_amount | number}}</div>
                                </div>
                            </div>
                            <div
                                style="height: 140px; background: url(../images/header-bg1.png) no-repeat scroll 85% center;"
                                class="col-lg-5 col-md-4 col-sm-4 text-right">
                                <div
                                    style="margin:35px 0 0 0; padding-right: 150px; font-family:OMUPro-Light; color:#FFFFFF;">
                                    <div style="font-size: 26px; line-height: 38px;">Total Traffic</div>
                                    <div style="font-size: 18px; line-height: 28px;">{{counter.sum_counter | number}}</div>
                                </div>
                            </div>
<!--                            <div class="col-lg-4 col-md-4 col-sm-4 text-left">
                                <form method="post" id="filter_search">
                                    <div style="display:block" id="state">
                                        <div style="padding-top:45px;">
                                            <select name="search_by" class="search-by"
                                                    ng-model="filterCondition.operator"
                                                    ng-options="state.short_code as state.state for state in states | filter:search | uppercase"
                                                    ng-change="ChangedStateReport(states, filterCondition.operator)">

                                            </select>
                                        </div>
                                    </div>

                                </form>
                            </div>-->
                        </div>
                    <?php } ?>
                    <div class="content-area">
                        <div class="pad-left-right" align="right">
                        	<span>

                            </span>
                            <span ng-show="id_month_select">
                            	<select class="list-field"  ng-model="dataM.selectedOption" ng-options="month_option.name as month_option.value for month_option in month_options" ng-change="change_month()">
                                                                     </select>
                            </span>
                          <span>
                       	  <select class="list-field" name="req_type"  ng-model="dataRT.selectedOption"  ng-options="require_type.value as require_type.name for require_type in require_types" ng-change="req_type_change()" >

                              </select>
                          </span>
                            <span>
                            	<a  ng-click="exelDownload()" ><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/excel.png"
                                                 width="23" height="23" alt=""/></a>
                                        </span>
                        </div>
                        <hr style="width:96%; margin:10px auto;">
                       <div align="center"  id="report_screen"> <!--  id="report_screen"-->
                            <table width="96%" border="0" cellspacing="0" class="content-table">
                                <thead>
                                <tr class="date-row">
                                    <td>Date</td>
                                    <td>&nbsp;</td>
                                    <td ng-repeat="vehical_type in vehical_types">{{vehical_type.type}}</td>

                                </tr>
                                </thead>
                                <tbody >

                                <tr class="date-row" ng-repeat="report in H_reports">
                                <td  class="border-dark">{{report.date | date : requestDateFormat()}}</td>
                                <td class="border-light"><div>Amount</div><div style="border-color:#D1D1D1;	border-style:solid;	border-width:1px 0 0px 0px;">Traffic</div></td>

                                         <td class="border-light" ng-repeat="vehical_type in vehical_types"><div>{{report['amount_'+vehical_type.vechical_types_id] || 0 }}</div><div style="border-color:#D1D1D1;	border-style:solid;	border-width:1px 0 0px 0px;">{{report['counter_'+vehical_type.vechical_types_id] || 0 }}</div></td>

                                </tr>
                                </tbody>
                            </table>
                            <div align="right" class="pad-left-right">Average {{reqs_type}} collection <!--from 1st Mar to till date-->
                                is - <strong>{{average | number}}</strong></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content row end -->
<?php
//print_r(ArrayHelper::toArray($history));
?>
<script type="application/javascript">
    var reports =  <?php echo json_encode(ArrayHelper::toArray($history), true); ?>;
    var vehical_types =  <?php echo json_encode(ArrayHelper::toArray($vehical_types), true); ?>;
    var base_url = '<?php echo Yii::$app->request->baseUrl; ?>';
    var pathInfo = '<?php echo Yii::$app->request->pathInfo; ?>';
    var toll_id = '<?php echo empty($toll_details->toll_id)? null : $toll_details->toll_id; ?>';
    var month_options = <?php echo json_encode($month_options); ?>;
    var counters = <?php echo json_encode($counter); ?>;
    var total_count = <?php echo $count; ?>;
    var toll_user_id = '<?php echo Yii::$app->user->identity->toll_employee_id;?>';
</script>