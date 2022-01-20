{if $customer.id}
    {if $message  =='true' }
        <div class="alert alert-success" role="alert">
            <p class="alert-text">Dėkojame už pastabą! </p>
        </div>
    {elseif $message == "false"}
        <div class="alert alert-danger" role="alert">
            <p class="alert-text">Klaida! </p>
        </div>
    {/if}

    <form action="" method="post">
        <fieldset class="form-group">
            <label class="form-control-label" for="exampleInput1">Radote klaidų? Praneškite!</label>
            <textarea required name="note" class="form-control" id="note" cols="20" rows="5"></textarea>
            <input type="hidden" name="id_product" value="{$product.id_product}">
            <input type="hidden" name="id_user" value="{if $customer}{$customer.id}{/if}">
        </fieldset>
        <br>
        <input type="submit" class="btn btn-primary-outline" value="Pranešti">
    </form>
{else}
    <p>
        <a href="{$urls.pages.authentication}">
            <button class="btn btn-primary-outline">
                Praneškite apie klaidingą infromaciją
            </button>
        </a>
    </p>
{/if}