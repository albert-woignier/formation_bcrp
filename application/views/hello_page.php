
<div class="starter-template">
    <h1></h1>
    <p><?php //echo $commentaires;             ?></p>
</div>

<div style="align-content: center;margin:auto;">

    <?php if ($this->session->data_base_group != 'bcrp') : ?>
        <img class="mb-4" src="<?php echo base_url(); ?>assets/img/logo_ghost.png" alt="logo fantome" >
        <p style="color:blue;font-size:1.3rem;">
           <strong>Pour information vous êtes pour l'instant sur le site de test. C'est votre terrain d'apprentissage de l'application. Vous pouvez y faire ce que vous voulez sans risquer de casser la version officielle.</strong>
        </p>
    <?php else : ?>
        <img class="mb-4" src="<?php echo base_url(); ?>assets/img/logobcrp.gif" alt="logo BCRP" >
        <p style="color:blue;font-size:1.3rem;">
            &nbsp;
        </p>
    <?php endif; ?>

    <h1 class="h3 mb-3 font-weight-normal">BCRP<br>Droit à la Formation</h1>

    <p>
        Bonjour <?php echo $this->session->user_prenom . ' ' . $this->session->user_nom; ?>, <br>bonne navigation sur le site interactif de formation à la pratique des disciplines du billard.
    </p>


    <p>
        &nbsp;
    </p>
    <p>
        &nbsp;
    </p>
</div>
<p>
    &nbsp;
</p>
<!---
Pour savoir si on est sur tablette (pdf ne s'affichent pas) ou pas
-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.2.4/pdfobject.min.js"></script>
<script>
    var pdf_ok = 4;
    if (PDFObject.supportsPDFs) {
        pdf_ok = 1;
    } else {
        pdf_ok = 0;
    }
    var iWindowsSize = $(window).width();
    $.post('<?php echo site_url('login/ajax_get_device') ?>', {pdf_ok: pdf_ok, view_port: iWindowsSize},
    function (data) {
        // alert(data);
    }, "text");
</script>






