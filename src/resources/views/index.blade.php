@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9" id="access-requests-main">

        You can request for access to another business&apos; Hub account using the <strong>Request Access</strong> tab. The <strong>Access Requests</strong> tab allows you to manage the status of existing requests:
        <ul class="nav nav-tabs nav-justified">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#request_access">Request Access</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#access_requests">Access Requests</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane container active" id="request_access">
                <br/>
                <form method="get" action="" v-on:submit.prevent="searchBusinesses">
                	<div class="row">
                        <div class="form-group col-md-12">
                            <input class="form-control" id="title" type="text" name="title" v-model="search_term" required v-bind:readonly="searching">
                            <label for="title">Search Business by their exact Email Address</label>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" name="search_businesses" value="1" v-bind:class="{'btn-loading': searching}">Search</button>
                        </div>
                    </div>
                </form>
                <div class="row" v-if="businesses.length > 0">
                    <access-grant-company-card class="col s12 m4" v-for="(company, index) in businesses" :key="company.id" :company="company" :index="index" v-on:request-modules="showModuleRequestDialog"></access-grant-company-card>
                </div>
                @include('modules-access-requests::modals.request-access')           
            </div>
            <div class="tab-pane container" id="access_requests">
                <br/>
		        <div class="container" id="listing_access_requests">
		            <div class="row mt-3" v-show="grants.grants.length > 0">
		                <access-grant-card class="col s12 m4" v-for="(grant, index) in grants.grants" :key="grant.id" :grant="grant" :index="index"></access-grant-card>
		            </div>
		            <div class="col s12" v-if="grants.grants.length === 0">
		                @component('layouts.blocks.tabler.empty-fullpage')
		                    @slot('title')
		                        No (existing) Requests
		                    @endslot
		                    To gain access to a business&apos; Hub modules, you first need to place a request.
		                    @slot('buttons')
		                        <a href="#" v-on:click.prevent="openTab('request_access')" class="btn btn-primary btn-sm">Request Access</a>
		                    @endslot
		                @endcomponent
		            </div>
                    <div class="row" v-if="grants.grants.length === 0 && grants.is_processing">
                    	<div class="loader"></div>
                    	<div>Loading Apps</div>
                    </div>
		        </div>
            </div>
        </div>

    </div>

</div>


@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#access-requests-main',
            data: {
                search_term: '',
                searching: false,
                businesses: [],
                available_modules: {!! json_encode($availableModules) !!},
                business: {},
                grants: {
                    grants: [],
                    meta: [],
                    page_number: 1,
                    is_processing: false,
                },
                requesting: false
            },
            mounted: function () {
                this.fetchRequests();
            },
            methods: {
            	openTab: function (tab) {
            		$('.nav-tabs a[href="#' + tab + '"]').tab('show');
            	},
            	openTabOnLoad: function() {
					var url = document.location.toString();
					if (url.match('#')) {
					    $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
					}
            	},
                requestAccess: function() {
                    this.requesting = true;
                },
                searchBusinesses: function () {
                    let context = this;
                    this.searching = true;
                    axios.get("/mpa/access-business-search", {
                        params: {search: context.search_term}
                    }).then(function (response) {
                        //console.log(response);
                        context.searching = false;
                        if (response.data.total == 0) {
                            return swal("Oops!", 'No matching businesses were found.', "info");
                        } else {
                            context.businesses = response.data.rows;
                        }
                    }).catch(function (error) {
                        let message = '';
                        if (error.response) {
                            // The request was made and the server responded with a status code
                            // that falls out of the range of 2xx
                            //var e = error.response.data.errors[0];
                            //message = e.title;
                            var e = error.response;
                            message = e.data.message;
                        } else if (error.request) {
                            // The request was made but no response was received
                            // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                            // http.ClientRequest in node.js
                            message = 'The request was made but no response was received';
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            message = error.message;
                        }
                        context.searching = false;
                        return swal("Oops!", message, "warning");
                    });
                },
                showModuleRequestDialog: function (index) {
                    let business = this.businesses.length  > 0 && typeof this.businesses[index] !== 'undefined' ? this.businesses[index] : null;
                    if (business === null) {
                        this.business = {};
                        return;
                    }
                    this.business = business;
                    $('#request-access-modal').modal('show');
                },
                changePage: function (number) {
                    this.page_number = parseInt(number, 10);
                    this.loadRequests();
                },
                fetchRequests: function () {
                    let context = this;
                    this.grants.is_processing = true;
                    this.grants.grants = [];
                    axios.get("/mpa/access-grants-for-user", {
                        params: {
                            limit: 12,
                            page: context.page_number
                        }
                    }).then(function (response) {
                        //console.log(response.data);
                        context.grants.is_processing = false;
                        context.grants.grants = response.data.data;
                        context.grants.meta = response.data.meta;
                    }).catch(function (error) {
                        let message = '';
                        context.grants.is_processing = false;
                        if (error.response) {
                            // The request was made and the server responded with a status code
                            // that falls out of the range of 2xx
                            //var e = error.response.data.errors[0];
                            //message = e.title;
                            var e = error.response;
                            message = e.data.message;
                        } else if (error.request) {
                            // The request was made but no response was received
                            // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                            // http.ClientRequest in node.js
                            message = 'The request was made but no response was received';
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            message = error.message;
                        }
                        return swal("Oops!", message, "warning");
                    });
                },
            }
        })
    </script>
@endsection
