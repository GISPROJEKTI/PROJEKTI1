var App = Backbone.View.extend({
    events: {
        "change input, select, textarea": "updateModel"
    },
    initialize: function(data) {
        _.bindAll(this, "render", "updateModel");
        $.template("pollTmpl", $("#pollTmpl"));
        // this.model = new Poll(data.poll);
        this.poll.bind("change", "render");
        this.render();
    },
    render: function() {
        var html = $.tmpl( "pollTmpl", this.poll.toJSON() );
        this.el.html( html );
        return this;
    },
    updateModel: function(evt) {
        
    }
});

jQuery(function($){
    var app = new App({
        el: $("#poll"),
        poll: new Poll(data.Poll)
    });

// LiveController.create({
//     el: $("#poll"),

//     elements: {
//         "#questions": "questionsEl"
//     },

//     events: {
//         "change input, textarea": "changed"
//     },

//     proxied: ["render", "changed"],

//     init: function(data) {
//         this.initLive();
//         console.info("app init");
//         $.template("pollTmpl", $("#pollTmpl"));
//         this.poll = Poll.init(data.Poll);
//         this.poll.bind("refresh change", this.render);
//         this.render();
//         // this.sidebar = Sidebar.init({el: this.sidebarEl});
//         // this.contact = Contacts.init({el: this.contactsEl});
//     },
//     render: function() {
//         this.el.html($.tmpl("pollTmpl", this.poll));
//         return this;
//     },
//     changed: function(e) {
//         var target = e.currentTarget,
//             value = $(target).val();
//         console.info({target: value});
//     }
// }).init(data);
// console.info(window.App);
// window.App.include(Live);


});