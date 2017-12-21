<!-- Logo -->
<figure class="text-center">
    <a href="./index.html">
        <img class="img-logo" src="images/logo.png" alt="">
    </a>
</figure> <!-- /.text-center -->

<!-- Title -->
<h1>AirDrop</h1>
<!-- Sub title -->
<p>Wallet:
    {% if is_online %}
    <b style="color: lightgreen">On-Line</b>
    {% else %}
    <b style="color: lightcoral">Off-Line</b>
    {% endif %}
    , Participants: <b>{{ registered }}</b>, Balance: <b>{{ balance }} ZEN</b></p>
{% if is_allowed %}

<p><?php $this->flashSession->output() ?></p>

<form id="submit" class="form-inline" method="post" action="/send">
    <div class="form-group">
        <input type="text" required name="address" class="form-control text-danger lar" id="address" placeholder="T Address">
    </div>
    <p>&nbsp;</p>
    <input data-sitekey="{{ recaptcha_sitecode }}" data-callback="onSubmit" type="submit"
            class="btn btn-danger g-recaptcha" value="Receive Airdrop"/>
</form>
<script>
    function onSubmit(token) {
        document.getElementById("submit").submit();
    }
</script>
{% else %}
You have been already registered.
{% endif %}
<p>&nbsp;</p>
<p>&nbsp;</p>