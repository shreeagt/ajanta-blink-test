<style>
    .cancel_pro {
        float: right;
        cursor:pointer;
    }
</style>
@if (Session::get('success', false))
    <?php $data = Session::get('success'); ?>
    @if (is_array($data))
        @foreach ($data as $msg)
            <div class="alert alert-success hide_alert_box_pro" role="alert">
                <i class="fa fa-check"></i>
                {{ $msg }}
                <span class="cancel_pro">&#10006;</span>
            </div>
        @endforeach
    @else
        <div class="alert alert-success hide_alert_box_pro" role="alert">
            <i class="fa fa-check"></i>
            {{ $data }}
            <span class="cancel_pro">&#10006;</span>
        </div>
    @endif
@elseif(Session::get('error', false))
    <?php $data = Session::get('error'); ?>
    @if (is_array($data))
        @foreach ($data as $msg)
            <div class="alert alert-error" role="alert">
                <i class="fa fa-check"></i>
                {{ $msg }}
            </div>
        @endforeach
    @else
        <div class="alert alert-error" role="alert">
            <i class="fa fa-check"></i>
            {{ $data }}
        </div>
    @endif
@endif
<script>
    var cancel_pro = document.getElementsByClassName('hide_alert_box_pro');console.log(cancel_pro);
    if(cancel_pro==''){
console.log("done");
    }
    cancel_pro[0].addEventListener("click", function() {
        document.getElementsByClassName("hide_alert_box_pro")[0].style.display = "none";
    });
    
</script>
