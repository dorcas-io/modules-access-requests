<div class="modal fade" id="request-access-modal" tabindex="-1" role="dialog" aria-labelledby="request-access-modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="request-access-modalLabel">Request Access to Modules</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="" id="form-request-access" method="post">
            {{ csrf_field() }}
            <h4>Select the Modules</h4>
            <fieldset class="form-fieldset">
                <div class="row">
                    <div class="form-group col-md-6 col-lg-4" v-for="module in available_modules" :key="module.id" v-if="!module.is_readonly">
                        <label class="custom-switch">
                            <input type="checkbox" name="modules[]" multiple v-bind:value="module.id" v-bind:checked="!module.enabled" class="custom-switch-input">
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description">@{{ module.name }}</span>
                        </label>
                    </div>
                </div>
            </fieldset>
            <input type="hidden" v-model="business.id" name="business_id" v-if="typeof business.id !== 'undefined'" />
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" name="action" form="form-request-access" value="request_access" class="btn btn-primary">Send Request</button>
      </div>
    </div>
  </div>
</div>