let SETTINGS = {};
const TYPES = {
  "journal-article": "article",
  "magazine-article": "magazine",
  "book-chapter": "chapter",
  publication: "article",
  "doctoral-thesis": "students",
  "master-thesis": "students",
  "bachelor-thesis": "students",
  "guest-scientist": "guests",
  "lecture-internship": "guests",
  "student-internship": "guests",
  reviewer: "review",
  editor: "editorial",
  monograph: "book",
  misc: "misc-annual",
  "edited-book": "book",
};

// function toggleExamples(subtype) {
//     $('#examples').find('[data-visible]').hide()
//     const vis = $('#examples').find('[data-visible="' + subtype + '"]')
//     if (vis.length === 0) {
//         $('#examples').find('[data-visible="none"]').show()
//     } else {
//         vis.show()
//     }
// }

// fetch(ROOTPATH + '/settings')
//     .then((response) => response.json())
//     .then((json) => SETTINGS = json);

function togglePubType(type, callback = () => {}) {
  type = type.trim().toLowerCase().replace(" ", "-");
  type = TYPES[type] ?? type;
  console.log(type);

  $("#type").val(type);
  $("#type-description").empty();
  $("#type-examples").empty();

  // select data
  let SELECTED_CAT = null;
  let SELECTED_TYPE = null;

  $.ajax({
    type: "GET",
    url: ROOTPATH + "/settings/activities",
    data: {
      type: type,
    },
    dataType: "json",
    success: function (response) {
        const data = response.data
      // console.log(response);
      SELECTED_CAT = data.category;
      SELECTED_TYPE = data.type;

      const SELECTED_MODULES = SELECTED_TYPE.modules;
      console.log(SELECTED_TYPE);

      $("#type").val(SELECTED_CAT.id);
      $("#subtype").val(SELECTED_TYPE.id);

      // add description (always visible)
      var descr = "";
      descr += lang(
        SELECTED_TYPE.description ?? "",
        SELECTED_TYPE.description_de ?? ""
      );
      if (descr != "") descr = "<i class='ph ph-info'></i> " + descr;
      $("#type-description").html(descr);

      var examples = SELECTED_TYPE.example ?? "";
      console.log(SELECTED_TYPE);
      $("#type-examples").html(examples);

      // show correct subtype buttons
      var form = $("#publication-form");
      form.find(".select-btns").hide();
      form.find('.select-btns[data-type="' + SELECTED_CAT.id + '"]').show();

      $(".select-btns").find(".btn").removeClass("active");
      $(".select-btns")
        .find(
          '.btn[data-subtype="' +
            SELECTED_TYPE.id +
            '"],.btn[data-type="' +
            SELECTED_CAT.id +
            '"]'
        )
        .addClass("active");

      $.ajax({
        type: "GET",
        url: ROOTPATH + "/settings/modules",
        data: {
          id: ID,
          modules: SELECTED_MODULES,
          copy: COPY ?? false,
        },
        dataType: "html",
        success: function (response) {
          // console.log(response);
          $("#data-modules").html(response);
          // if (SELECTED_MODULES.includes('title')) {
          $(".title-editor").each(function (el) {
            var element = this;
            // initQuill(element)

            var authordiv = $(".author-list");
            if (authordiv.length > 0) {
              authordiv.sortable({});
            }
          });
          // }

          callback();
          console.log("TEST");
          $("#data-modules")
            .find(":input")
            .on("change", function () {
              console.log("test");
              doubletCheck();
            });
        },
      });

      form.slideDown();
      return;
    },
  });

  // show form
}

const convertArrayToObject = (array, key) => {
  if (!Array.isArray(array)) return array;
  const initialValue = {};
  return array.reduce((obj, item) => {
    return {
      ...obj,
      [item[key]]: item,
    };
  }, initialValue);
};

function activeButtons(type) {
  $(".select-btns").find(".btn").removeClass("active");

  $("#" + type + "-btn").addClass("active");
  switch (type) {
    case "publication":
      $("#article-btn").addClass("active");
      break;
    case "review":
      $("#review2-btn").addClass("active");
      break;
    case "misc":
      $("#misc-once-btn").addClass("active");
      break;

    case "students":
      $("#students2-btn").addClass("active");
      break;
    case "guests":
      $("#students-btn").addClass("active");
      break;
    case "editorial":
    case "grant-rev":
    case "thesis-rev":
      $("#review-btn").addClass("active");
      break;
    case "misc-once":
    case "misc-annual":
      $("#misc-btn").addClass("active");
      break;
    case "article":
    case "magazine":
    case "book":
    case "chapter":
    case "preprint":
    case "dissertation":
    case "others":
      $("#publication-btn").addClass("active");
      break;
    default:
      break;
  }
}
