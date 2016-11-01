<?php
/**
 * Created by PhpStorm.
 * User: Kesav
 * Date: 1/21/2016
 * Time: 2:32 PM
 */
$this->title = 'Tollr';
?>
<div ng-view ng-app="UserApp" ng-controller="Home">
    <div id="header">
        <div class="logo"><img src="<?php echo Yii::$app->homeUrl; ?>images/logo.png" width="123" height="53"/></div>
        <div class="login-info">
            <div>
                <?php //echo Yii::$app->request->hostInfo.'Toll_Dev/common/profile_pic/'.'user_'.Yii::$app->user->identity->user_id.'.jpg'; ?>
                <div
                    class="cap"><?php echo Yii::$app->user->identity->firstname . " " . Yii::$app->user->identity->lastname; ?></div>
                <div class="user" style="height:60px;width:73px" ng-style="{'background-color':logincolor}"
                     set-class-when-at-bottom="fix-to-bottom" popover-placement="bottom"
                     popover-is-open="{{popoverIsOpen}}" ng-mouseleave="closeSignoutMenu()"
                     ng-mouseenter="openSignoutMenu()" ng-click="openSignoutMenu()"><img
                        src="<?php if (file_exists(Yii::$app->request->hostInfo . '/Tolls_Dev/common/profile_pic/' . 'user_' . Yii::$app->user->identity->user_id . '.jpg') !== false) {
                            echo Yii::$app->request->hostInfo . '/Tolls_Dev/common/profile_pic/' . 'user_' . Yii::$app->user->identity->user_id . '.jpg';
                        } else {
                            echo Yii::$app->homeUrl . 'images/user.png';
                        } ?>" width="43" height="42" class="img-circle"/></div>
                <div class="popover fade {{popoverIsOpen}} bottom" style="top: 80px; left:84%;"
                     ng-mouseenter="openSignoutMenu()" ng-mouseleave="closeSignoutMenu()">
                    <div class="arrow"></div>

                    <div class="popover-inner">
                        <!-- ngIf: title -->
                        <div tooltip-template-transclude-scope="originScope()"
                             uib-tooltip-template-transclude="contentExp()" class="popover-content">
                            <div style="margin: 20px;" class="form-group signout-menu ng-scope">
                                <a class="btn myButton" href="<?php echo Yii::$app->homeUrl; ?>site/logout">Sign Out</a>
                            </div>
                        </div>
                    </div>
                </div>
                <script type="text/ng-template" id="myPopoverTemplate.html">
                    <div class="form-group signout-menu" style="margin: 20px;">
                        <button class="btn myButton">Sign Out</button>
                    </div>
                </script>
                <!--<div class="user"><img src="images/user.png" width="37" height="36"/></div>-->
            </div>

        </div>
    </div>
    <toll-tabs>
        <toll-panes title="Trip History">
            <div ng-controller="History">
                <div id="breadcrumbs">
                    <div class="page-heading">My Trip History</div>
                    <div class="search">
                        <div class="row col-md-12">
                            <div class="col-md-5">
                                <p class="input-group">
                                    <input type="text" class="form-control" uib-datepicker-popup="{{format}}"
                                           ng-model="from_date"
                                           is-open="popup1.opened" min-date="minDate" max-date="maxDate"
                                           datepicker-options="dateOptions" date-disabled="disabled(date, mode)"
                                           ng-required="true"
                                           close-text="Close" alt-input-formats="altInputFormats"/>
                                    <span class="input-group-btn">
                <button type="button" class="btn btn-default" ng-click="open1()"><i
                        class="glyphicon glyphicon-calendar"></i></button>
              </span>
                                </p>
                            </div>

                            <div class="col-md-5">
                                <p class="input-group">
                                    <input type="date" class="form-control" uib-datepicker-popup ng-model="to_date"
                                           is-open="popup2.opened" min-date="minDate" max-date="maxDate"
                                           datepicker-options="dateOptions" date-disabled="disabled(date, mode)"
                                           ng-required="true"
                                           close-text="Close"/>
                                    <span class="input-group-btn">
                <button type="button" class="btn btn-default" ng-click="open2()"><i
                        class="glyphicon glyphicon-calendar"></i></button>
              </span>
                                </p>
                            </div>
                            <span class="col-md-1"><button class="btn myButton"
                                                           ng-click="findHistory(from_date, to_date)">FIND</button></span>
                        </div>
                    </div>
                    <!--<div class="search"><span><input name="" type="date" style="padding:5px;" placeholder="Start Date" ng-model="from_date.value"  /></span><span><input name="" type="date" placeholder="End Date" style="padding:5px;" ng-model="to_date.value" /></span><span><a href="#" class="myButton">FIND</a></span></div>-->
                </div>
                <div class="content">
                    <div class="tab-header row">
                        <span class="col-md-3">Date</span><span class="col-md-6">Trip</span><span class="col-md-2">Status</span><span
                            class="col-md-1">Details</span>
                    </div>
                    <uib-accordion close-others="oneAtATime">

                        <uib-accordion-group ng-repeat="historydetails in history" is-open="status.open">
                            <uib-accordion-heading>
                                <div class="tab-row row"><span
                                        class="col-md-3">{{historydetails.travel_date}}</span><span
                                        class="col-md-6">{{historydetails.from_location}} - {{historydetails.to_location}}</span><span
                                        class="col-md-2">{{historystatus(historydetails.trip_stahistorydetails.tus)}}</span><span
                                        class="col-md-1"><i
                                            class="glyphicon glyphicon-print"></i><i class="pull-right glyphicon"
                                                                                     ng-class="{'glyphicon-minus': status.open, 'glyphicon-plus': !status.open}"></i></span>
                                </div>
                            </uib-accordion-heading>
                            <div class="tab-header row">
                                <span class="col-md-3">Date Time</span><span class="col-md-4">Toll Name</span><span
                                    class="col-md-4">Amount</span><span class="col-md-1"></span>
                            </div>
                            <div class="tab-row row" ng-repeat="tripdetail in tripdetails[historydetails.trip_id]"><span
                                    class="col-md-3">{{tripdetail.travel_date}}</span><span class="col-md-4">{{tripdetail.toll_name}}</span><span
                                    class="col-md-4"><i class="fa fa-rupee"></i> {{tripdetail.amount}}</span><span
                                    class="col-md-1"><i
                                        class="glyphicon glyphicon-print"></i></span></div>

                        </uib-accordion-group>


                </div>
            </div>
        </toll-panes>
        <toll-panes title="Trip Favorities">
            <div ng-controller="Favorite">
                <div id="breadcrumbs">
                    <div class="page-heading">My Favorities</div>
                    <div class="search">
                        <!--<div class="row col-md-12">
                            <div class="col-md-5">
                                <p class="input-group">
                                    <input type="text" class="form-control" uib-datepicker-popup="{{format}}"
                                           ng-model="from_date"
                                           is-open="popup1.opened" min-date="minDate" max-date="maxDate"
                                           datepicker-options="dateOptions" date-disabled="disabled(date, mode)"
                                           ng-required="true"
                                           close-text="Close" alt-input-formats="altInputFormats"/>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default" ng-click="open1()"><i
                            class="glyphicon glyphicon-calendar"></i></button>
                  </span>
                                </p>
                            </div>

                            <div class="col-md-5">
                                <p class="input-group">
                                    <input type="date" class="form-control" uib-datepicker-popup ng-model="to_date"
                                           is-open="popup2.opened" min-date="minDate" max-date="maxDate"
                                           datepicker-options="dateOptions" date-disabled="disabled(date, mode)"
                                           ng-required="true"
                                           close-text="Close"/>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default" ng-click="open2()"><i
                            class="glyphicon glyphicon-calendar"></i></button>
                  </span>
                                </p>
                            </div>
                            <span class="col-md-1"><button class="btn myButton" ng-click="findFavs(from_date, to_date)">FIND</button></span>
                        </div>-->
                    </div>
                    <!--<div class="search"><span><input name="" type="date" style="padding:5px;" placeholder="Start Date" ng-model="from_date.value"  /></span><span><input name="" type="date" placeholder="End Date" style="padding:5px;" ng-model="to_date.value" /></span><span><a href="#" class="myButton">FIND</a></span></div>-->
                </div>
                <div class="content">
                    <div class="tab-header row">
                        <span class="col-md-5">From</span><span class="col-md-5">To</span><span
                            class="col-md-2">name</span>
                        <!--<span
                            class="col-md-1">Details</span>-->
                    </div>
                    <uib-accordion close-others="oneAtATime">

                        <uib-accordion-group ng-repeat="favorite in favorites" is-open="status.open" is-disabled="true">
                            <uib-accordion-heading>
                                <div class="tab-row row"><span class="col-md-5">{{favorite.from_location}}</span>
                                    <span class="col-md-5">{{favorite.to_location}}</span>
                                    <span class="col-md-2">{{historystatus(favorite.nick_name)}}</span>
                                    <!--<span class="col-md-1"><i
                                            class="glyphicon glyphicon-print"></i><i class="pull-right glyphicon"
                                                                                     ng-class="{'glyphicon-minus': status.open, 'glyphicon-plus': !status.open}"></i></span>-->
                                </div>
                            </uib-accordion-heading>
                            <div class="tab-header row">
                                <span class="col-md-3">Date Time</span><span class="col-md-4">Toll Name</span><span
                                    class="col-md-4">Amount</span><span class="col-md-1"></span>
                            </div>
                            <div class="tab-row row" ng-repeat="tripdetail in tripdetails[favorite.trip_id]"><span
                                    class="col-md-3">{{tripdetail.travel_date}}</span><span class="col-md-4">{{tripdetail.toll_name}}</span><span
                                    class="col-md-4"><i class="fa fa-rupee"></i> {{tripdetail.amount}}</span><span
                                    class="col-md-1"><i
                                        class="glyphicon glyphicon-print"></i></span></div>

                        </uib-accordion-group>
                    </uib-accordion>

                </div>
            </div>
        </toll-panes>
        <toll-panes title="My Vechicals">
            <div ng-controller="Vehicals">
                <div id="breadcrumbs">
                    <div class="page-heading">My Vechicals</div>
                    <div class="search">
                        <div class="row col-md-12">
                            <div class="col-md-3"></div>
                            <div class="col-md-1" style="align: center">

                                <i class="fa fa-plus-circle fa-2x" ng-click="addinClass()"></i>


                            </div>
                            <div class="col-md-1" style="text-align: center; font-size: 18px">
                                |
                            </div>
                            <div class="col-md-5">
                                <p class="input-group col-md-12">
                                    <select class="form-control " ng-model="vehical_sele_type"
                                            ng-change="changetype(vehical_sele_type)"
                                            ng-options="type.vechical_types_id as type.type for type in vehicles_types">
                                        <option value="">Select Type</option>

                                    </select>
                                </p>
                            </div>
                            <span class="col-md-1"><button class="btn myButton" ng-click="findVehicles()">FIND</button></span>
                        </div>
                    </div>
                    <!--<div class="search"><span><input name="" type="date" style="padding:5px;" placeholder="Start Date" ng-model="from_date.value"  /></span><span><input name="" type="date" placeholder="End Date" style="padding:5px;" ng-model="to_date.value" /></span><span><a href="#" class="myButton">FIND</a></span></div>-->
                </div>
                <div class="content">
                    <div class="tab-header row">
                        <span class="col-md-3">Vechical No</span><span
                            class="col-md-3">Nick Name</span><span class="col-md-3">Vechical Type</span><span
                            class="col-md-2">Date Added</span><span
                            class="col-md-2"></span>
                    </div>
                    <div class="tab-row row" ng-repeat="vehicle in vehicles"><span
                            class="col-md-3">{{vehicle.registration_no}}</span><span
                            class="col-md-3">{{vehicle.vechical_nickname}}</span><span class="col-md-3">{{vechical_type(vehicle.vechical_type_id)}}</span><span
                            class="col-md-2">{{vehicle.created_on}}</span><span class="col-md-1"><span class="col-md-6"><i
                                    class="fa fa-pencil" ng-click="inEditedClass(vehicle.vechical_id)"></i></span><span
                                class="col-md-6"><i class="fa fa-remove"
                                                    ng-click="removeVehicle(vehicle.vechical_id)"></i></span></span>
                    </div>
                </div>
            </div>
        </toll-panes>
    </toll-tabs>


    <script>
        var base_url = "http://115.124.125.42/Tolls/api/2319/";
        // var username = " ";
        var user_id = <?php echo Yii::$app->user->identity->user_id; ?>;
        var history_c = <?php echo json_encode($history); ?>;
        var favs_c = <?php echo json_encode($favs); ?>;
        var vechicals_c = <?php echo json_encode($vechicals); ?>;
        var vehical_list_c = <?php echo json_encode($vehical_list); ?>;
        var vechicaal_types_c = <?php echo json_encode($vechicaal_types); ?>;
        var vechical_types_c = <?php echo json_encode($vechical_types); ?>;
    </script>
    <script type="text/ng-template" id="my-tabs.tpl.html">
        <div id="nav">
            <ul>
                <li ng-repeat="pane in panes">
                    <div class="nav" ng-class="{active:pane.selected}"><a href="#"
                                                                          ng-click="select(pane)">{{pane.title}}</a>
                    </div>
                </li>

            </ul>

        </div>
        <div class="tab-content" ng-transclude></div>
    </script>
    <script type="text/ng-template" id="toll-panes.tpl.html">
        <div class="tab-pane" ng-show="selected" ng-transclude>
        </div>
    </script>
    <div class="container">
        <!-- Modal -->
        <div class="modal fade {{inClass}}" id="myModal" role="dialog" style="display: {{displayN}}">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                ng-click="removeinClass()">&times;</button>
                        <h4 class="modal-title">Add Vehicle</h4>
                    </div>
                    <div class="modal-body">
                        <form name="vehiAdd">
                            <div class="form-group"
                                 ng-class="{ 'has-error' : vehiAdd.registration_no.$invalid && !vehiAdd.registration_no.$pristine }">
                                <input type="text" class="form-control" id=""
                                       placeholder="Enter Vehical Number (AP36AP7079)" ng-model="vehi.registration_no"
                                       name="registration_no" required ng-required="true">
                                <p ng-show="isRegNo" class="help-block he-reg-block">Registration No is required.</p>
                            </div>
                            <div class="form-group"
                                 ng-class="{ 'has-error' : vehiAdd.vechical_type_id.$invalid && !vehiAdd.vechical_type_id.$pristine }">
                                <select class="form-control " ng-model="vehi.vechical_type_id"
                                        ng-change="changetype(vehi.type)"
                                        ng-options="type.vechical_types_id as type.type for type in vehicles_types"
                                        name="vechical_type_id" required>
                                    <option value="">Select Type</option>

                                </select>
                                <p ng-show="isVehType" class="help-block has-error">Vehical type is required.</p>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="" placeholder="Enter Vehical Nick Name"
                                       ng-model="vehi.vechical_nickname" name="vechical_nickname">
                            </div>
                            <div class="form-group">
                                <input type="file" id="" placeholder="Enter Vehical Nick Name"
                                       ng-files="getTheFiles($files)">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default" ng-click="addVehicle(vehiAdd.$valid)">ADD</button>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal fade {{inEditClass}}" id="myModal" role="dialog" style="display: {{displayEditN}}">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                ng-click="removeinEditClass()">&times;</button>
                        <h4 class="modal-title">Edit Vehicle</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <div class="form-group">
                                <input type="text" class="form-control" id=""
                                       placeholder="Enter Vehical Number (AP36AP7079)" value="{{vehiEditre}}" readonly>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="" placeholder="" value="{{vehiEdittype}}"
                                       readonly>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" ng-model="vehi.vechical_nickname" id=""
                                       placeholder="Enter Vehical Nick Name"/>
                            </div>
                            <div class="form-group">
                                <input type="file" name="file" id="" ng-files="getTheFiles($files)"
                                       placeholder="Enter Vehical Nick Name">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"
                                ng-click="updatevech(vehiEditid)">Update
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
