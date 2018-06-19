             
                                    <form class="form-horizontal">
                                    <p  class="text-center">You have <span class="text-danger">XX </span>days remaining</p>
                                        <div class="row">
                                            <label class="col-md-3 label-on-left">Leave Type</label>
                                            <div class="col-md-9">
                                                <div class="form-group label-floating is-empty">
                                                    <label class="control-label"></label>
                                                    <select   class="form-control" name="leave-type-id" required>
                                                        <option value="">Select a leave type</option>
                                                        <option value="1">Annual Leave</option>
                                                        <option value="2">Other Leave types</option>
                                                    </select>
                                                        </div>
                                                
                                            </div>
                                        </div>



                                        <div class="row">
                                            <label class="col-md-3 label-on-left">Start date</label>
                                            <div class="col-md-9">
                                                <div class="form-group label-floating is-empty">
                                                    <label class="control-label"></label>
                                                    <input type="text" name="start-date" class="form-control" placeholder="Please select a leave type" required disabled/>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <label class="col-md-3 label-on-left">End date</label>
                                            <div class="col-md-9">
                                                <div class="form-group label-floating is-empty">
                                                    <label class="control-label"></label>
                                                    <input type="text" name="end-date" class="form-control"
                                                    placeholder="Please choose a start date first" required disabled/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-3 label-on-left">Duration</label>
                                            <div class="col-md-9">
                                                <div class="form-group label-floating is-empty">
                                                    <label class="control-label"></label>
                                                    <span>
                                                    <input type="text" class="form-control" name="duration"
                                                     placeholder="Duration in days" required disabled/>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                       
                                        <!-- <div class="row">
                                            <label class="col-md-3"></label>
                                            <div class="col-md-9">
                                                <div class="form-group form-button">
                                                    <button type="submit" class="btn btn-fill btn-rose">Sign in</button>
                                                </div>
                                            </div>
                                        </div> -->

                               
             
            <script>
             $(document).ready(function(){

                    var startDate = $("input[name=start-date]");
                    var endDate = $('input[name=end-date]');
                    var duration = $('input[name=duration]');
                    var maxdays= 30;

                    var today=moment(new Date());
                    var holidays=["2018-07-02","2018-06-19", "2018-06-25", "2018-07-01"];
                    var holidaysAsInt = [];
                    $(holidays).each(function(i){
                        holidaysAsInt.push(moment(holidays[i]).dayOfYear());
                    });
                    console.log(holidaysAsInt);

                    function initDatepicker(element,minDate, maxDate){
                        $(element).datepicker({
                            format : "yyyy-mm-dd",
                            startDate: moment(minDate).format('YYYY-MM-DD'),
                            endDate: moment(maxDate).format('YYYY-MM-DD'),
                            daysOfWeekDisabled:[0,6],
                            autoClose:true
                        });
                    }

                  function calculateStartDate(){

                  } 

                  function calculateEndDate(date, duration=maxdays){
                       var count=0;
                       var thisDate=date;
                       while(count < duration){
                        thisDate=moment(thisDate).add(1, "days");
                        var day = moment(thisDate).day();
                        if(day!==6&&day!==0)
                        {
                            var doy = moment(thisDate).dayOfYear();
                            if(!holidaysAsInt.includes(doy))
                            {
                                count++; 
                            }
                        }
                        
                       }
                       return thisDate; 
                  } 

                  function calculateDuration(){

                  }

                  function disableField($element, boolean, placehold, initial)
                  {
                    $element.datepicker("destroy");
                    var placeholder = "Choose a "+placehold+" date";
                    if(boolean){placeholder = "Select a "+initial+" first";}
                    $element.val(''); 
                    $element.attr('disabled',boolean);
                    $element.attr('placeholder',placeholder);
                  }

                function disableAllFields()
                {
                    disableField(startDate,true, "start date", "leave type");
                    disableField(endDate,true, "end date", "start date");
                    disableField(duration,true, "duration", "end date");
                }

                $('select').on('change',function(){ 
                  var type = $(this).val();
                   disableAllFields();
                    if(isNaN(type) || type==="")
                    {
                        return false;
                    }
                    var c= type==="1"? 14 : 1;
                    disableField(startDate,false, "start date", "leave type");
                    mindate=calculateEndDate(today,c);
                    maxdate=calculateEndDate(mindate);
                    initDatepicker(startDate,mindate,maxdate);
                });

                startDate.on('change', function(){

                    var enddate=calculateEndDate(this.value);
                    var mindate =calculateEndDate((this.value),1);
                    disableField(endDate,false, "end date", "start date");
                    disableField(duration,false, "duration", "end date");
                    initDatepicker(endDate,mindate,enddate);
                });

                endDate.on('change',function(){
                    var duration = calculateDuration();


                });



             	

             })
             </script>
           