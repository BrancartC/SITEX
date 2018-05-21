<section id="tpsem05">
    <form action="" name="formSearch" id="formSearch">
        <fieldset id="tp05search">
            <legend>Groupe recherché</legend>
            <input type="text" name="tp05Text" id="text" class="I" placeholder="Groupe recherché">
            <div>
                <span title="début (Begin)">
                    <label for="B">&#8676;</label><input type="radio" id="B" value="B" name="posSearch">
                </span>
                <span title="millieu (In)">
                    <label for="I">&#8676;</label><input type="radio" id="I" value="I" name="posSearch" checked><label
                            for="I">&#8677;</label>
                </span>
                <span title="fin (End)">
                    <input type="radio" id="E" value="E" name="posSearch"><label for="E">&#8677;</label>
                </span>
            </div>
        </fieldset>
    </form>
    <form action="formSubmit.html" name="suges" id="formTP05">
        <fieldset id="tp05select">
            <legend>Suggestion</legend>
            <select name="select" id="formSelect" title="choisissez le groupe à afficher" size="10">
                <option value="">a</option>
                <option value="" class="deja">b</option>
            </select>
        </fieldset>
    </form>
    <fieldset id="tp05result">
        <legend>Liste de cours</legend>
        <p>Pas de groupe sélectionné</p>
        <div></div>
    </fieldset>
</section>
<style>
    #tpsem05 {
        display: flex;
        flex-direction: row;
        flex-flow: row wrap;
    }

    #formSearch {
        text-align: center;
    }

    #formSearch label {
        margin: 0;
        font-size: 1.5em;
    }

    #text {
        height: 2em;
        width: 12em;
        font-size: 1.25em;
    }

    #text.E {
        text-align: right;
    }

    #text.I {
        text-align: center;
    }

    #text.B {
        text-align: left;
    }

    #formSearch span {
        margin: 0.25em;
        padding: 0.25em;
        box-shadow: 0.05em 0.05em darkgrey;
        -webkit-border-radius: 1em;
        -moz-border-radius: 1em;
        border-radius: 1em;
        background-color: rgba(255, 255, 255, 0.67);
    }

    #formSelect {
        width: 10em;
        overflow-y: scroll;

    }

    #formSelect option.deja::before {
        content: '\21AA \00a0';
    }

</style>