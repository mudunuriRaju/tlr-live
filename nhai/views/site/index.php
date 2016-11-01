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
                            <a class="navbar-brand navbar-brand-custom" href="#"><img
                                    src="<?php echo Yii::$app->request->baseUrl; ?>/images/logo.png" width="119"
                                    height="61" alt=""/></a>
                        </div>

                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse navbar-collapse-custom" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav">
                                <li class=""><a href="#">Home <span class="sr-only">(current)</span></a></li>
                                <li><a href="site/reports">Reports</a></li>
                            </ul>
                            <ul class="nav navbar-nav navbar-right">
                                <a class="navbar-brand navbar-brand-custom" style="float:right;" href="#"><img
                                        src="<?php echo Yii::$app->request->baseUrl; ?>/images/nha-logo.png" width="91"
                                        height="60" alt=""/></a>
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
            <div class="col-lg-6 col-md-6 col-sm-6 p0 tr">
                <div class="disinblo">
                    <img width="48" height="47" class="login-user-icon" alt=""
                         src="<?php echo Yii::$app->request->baseUrl; ?>/images/user.png">
                </div>
                <div class="login-user-text disinblo">
                    <div align="left" style="color:#ff9600; font-weight: 600;font-family: OMUPro-Light">Subba Raju</div>
                    <div align="left" style="padding-left: 10px;"><a
                            href="<?php echo Yii::$app->request->baseUrl; ?>/site/logout">Logout</a></div>
                </div>
            </div>

        </div>
    </div>
    <!-- content row start -->
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3 hidden-xs graphMain">
                <div class="graphBox totalCollection tc">
                    <div class="totalCollectionHeading">TOTAL COLLECTION</div>
                    <div class="totalCollectionAmount">₹ 24,52,025</div>
                </div>
                <div class="graphBox UserCompAnaly"><img width="280px"
                                                         src="<?php echo Yii::$app->request->baseUrl; ?>/images/groph2_360.png"/>
                </div>
                <div class="graphBox axelUsersAnaly"><img width="220px"
                                                          src="<?php echo Yii::$app->request->baseUrl; ?>/images/graph1_360.png"/>
                </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 rightPannel">
                <div class="rightPanBox">
                    <div class="header2">
                        <div class="col-lg-3 col-md-3 col-sm-3 hidden-xs">
                            <div style="margin:26px 0 0 0; font-family:OMUPro-Light; color:#FFFFFF;">
                                <div style="font-size:40px;">{{TollsIndiaCount}}</div>
                                <div style="font-size:22px; padding-left:8px;">Tollered</div>
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12" align="right">
                            <form>
                                <div id="dirBut" ng-class="directionBut" ng-click="changeShowState()"></div>
                                <div id="state" style="" ng-show="showState">
                                    <div style="padding-top:40px;">
                                        <select class="search-by" ng-model="filterCondition.operator"
                                                ng-options="state.short_code as state.state for state in states | filter:search | uppercase"
                                                ng-change="ChangedState(states, filterCondition.operator)">

                                        </select>
                                    </div>
                                </div>
                                <div id="direction" style="" ng-hide="showState">
                                    <div><input type="text" class="starting-point" placeholder="choose starting point"
                                                g-places-autocomplete ng-model="fromPoint"><span></span></div>
                                    <div><input type="text" class="disti-point" placeholder="choose destination"
                                                g-places-autocomplete ng-model="toPoint"><span><button
                                                class="point-point-but" ng-click="findTolls()">Find</button></span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="map-wrapper">
                        <div id="google-map" class="google-map">
                            <!--<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d8117.209640531918!2d24.755183321194423!3d59.42803077219139!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xf00b36df26c8300!2sEast+Tallinn+Central+Hospital!5e0!3m2!1sru!2sua!4v1410423193189" width="100%" height="100%" frameborder="0" style="border:0">
                            </iframe>-->
                        </div>
                        <div class="google-map-overlay" ng-show="showState">
                            <div class="grid">
                                <div class="grid-view-result">{{SelectedState}}</div>
                                <div class="grid-view-result1">{{ListedTollsCount}} Tollered listed</div>
                                <div>
                                    <input class="txt-fld" name="" placeholder="search concessioner name" type="text"
                                           ng-model="tollName" ng-change="searchToll(tollList,tollName)"/>
                                </div>
                                <div class="grid-list">
                                    <div style="height:30px;"></div>
                                    <div class="grid-view-concessioner-list" ng-repeat="toll in searchedList"><a
                                            href="site/report?id={{toll.toll_id}}"
                                            style="text-decoration: none; color:#181818">
                                            <div>{{toll.toll_name}} / {{toll.toll_stretch}}</div>
                                            <div>{{toll.toll_location}}</div>
                                        </a></div>

                                </div>
                            </div>
                            <div class="but" ng-show="showState"></div>
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
</script>