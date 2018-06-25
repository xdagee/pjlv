$(document).ready(function(){

    var $startDate = $("input[name=start-date]");
    var $endDate = $('input[name=end-date]');
    var $duration = $('input[name=duration]');
    var maxdays= 30;

    var today=moment().format("YYYY-MM-DD");
    var holidays=["2018-07-02","2018-06-19", "2018-06-25", "2018-07-02"];
    var holidaysAsInt = [];
    $(holidays).each(function(i){
        holidaysAsInt.push(moment(holidays[i]).dayOfYear());
    });


    $endDate.add($startDate).on('keypress',function (e) {
        e.preventDefault();
        return false;
    });

    function initDatepicker(element,minDate, maxDate){
        $(element).datepicker({
            format : "yyyy-mm-dd",
            keyboardNavigation: false,
            startDate: minDate,
            endDate: maxDate,
            weekStart: 1,
            daysOfWeekDisabled:[0,6],
            datesDisabled: holidays,
            autoclose:true
        }).on('changeDate', function(){
            setTimeout(function(){$(element).trigger("blur");},50);
        });

    }

    function calculateEndDate(date, duration=maxdays)
    {
        var count=0;
        var thisDate=moment(date);
        while(count < duration){
            thisDate=thisDate.add(1, "days");
            var day = moment(thisDate).day();
            if(day!==6&&day!==0)
            {
                var doy = thisDate.dayOfYear();
                if(!holidaysAsInt.includes(doy))
                {count++;}
            }

        }
        return thisDate.format('YYYY-MM-DD');
    }

    function calculateDuration(start, end, duration=0)
    {
        var days = 0;
        var startm = moment(start);
        var endm = moment(end);
        if(duration===0)
        {
            while(startm.isBefore(endm))
            {
                var day = startm.day();
                var doy = startm.dayOfYear();

                if(day!==6 && day!==0 && !holidaysAsInt.includes(doy))
                {
                    days++;
                }
                startm = startm.add(1,'days');
            }
            return days;
        }
        else
        {
            return calculateEndDate(start,duration);
        }
    }

    function disableField($element, boolean, placehold, initial)
    {
        $element.datepicker("destroy");
        var placeholder = "Choose "+placehold;
        if(boolean){placeholder = "Select a "+initial+" first";}
        $element.val('');
        $element.attr('disabled',boolean);
        $element.attr('placeholder',placeholder);
    }

    function disableAllFields()
    {
        disableField($startDate,true, "start date", "leave type");
        disableField($endDate,true, "end date", "start date");
        disableField($duration,true, "duration", "end date");
    }

    function readableDate(element)
    {
        var dateVal = $(element).val().substr(0,10);
        var dateString = moment(dateVal).format("dddd, Do MMMM  YYYY");
        if(dateString!=="Invalid date")
        {
            $(element).val(dateVal + " (" + dateString + " )");
        }
    }

    function writeDuration(days) {
        return days+ " day(s)";
    }

    $('select').on('change',function(){
        var type = $(this).val();
        disableAllFields();
        if(isNaN(type) || type==="")
        {
            return false;
        }
        var c= type==="1"? 14 : 1;
        disableField($startDate,false, "start date", "leave type");
        mindate=calculateEndDate(today,c);
        maxdate=calculateEndDate(mindate);
        initDatepicker($startDate,mindate,maxdate);
    });

    ($startDate).add($endDate).on("blur", function () {
        readableDate(this);
    });


    $startDate.on('change', function(){
        var start = this.value;
        var enddate=calculateEndDate(start);
        var mindate =calculateEndDate((this.value),1);
        disableField($endDate,false, "end date", "start date");
        disableField($duration,false, "duration", "end date");
        initDatepicker($endDate,mindate,enddate);

    });

    $endDate.on('change',function(){
        var duration = calculateDuration($startDate.datepicker("getDate"), $(this).datepicker("getDate"));
        readableDate(this);
        $duration.val(writeDuration(duration));
    });



    $duration.on('click', function () {
        var val = $(this).val();
        var sp = val.indexOf(" ");
        if(sp!==-1)
        {
            val = val.substr(0,sp);
        }
        $(this).val(val);
    });

    function durationError(error)
    {
        $duration.parent().after("<p class='alert alert-danger'>"+ error +"</p>");
        setTimeout(function () {
            $("p.alert-danger").jAnimate("fadeOut");
        }, 1500);

        setTimeout(function () {
            $("p.alert-danger").remove();
        }, 2000);

    }

    $duration.on('blur', function(){
        var val = $(this).val();
        if(Math.sign(val.indexOf("d"))!==-1 || val==="" || val==="0" || isNaN(val) )
        {
            return false;
        }
        $(this).val(writeDuration($(this).val()));
    });

    $duration.on('change', function () {
        var val = $(this).val();
        if(Math.sign(val)===-1 || val===0 || isNaN(val) || val==="" || val==="0")
        {
            $endDate.val("");
            $(this).val("");
            durationError("Only valid numbers are allowed");
            return false;
        }
        if(val>maxdays)
        {
            durationError("You cannot select more than allowable leave days");
            $duration.val(maxdays);
            val = maxdays;
        }

        var enddate = calculateDuration($startDate.datepicker("getDate"),null ,val);
        $endDate.val(enddate).trigger('blur');
        $(this).val(writeDuration(val));
        $(this).blur();
    });

});