// Core packages
const gulp = require("gulp"),
    minimist = require("minimist"),
    log = require("fancy-log"),
    plumber = require("gulp-plumber"),
    concat = require("gulp-concat");

// Sass and CSS packages
const sass = require("gulp-sass"),
    sassGlob = require("gulp-sass-glob"),
    postCSS = require("gulp-postcss"),
    autoprefixer = require("autoprefixer"),
    cleanCSS = require("gulp-clean-css");

// Javascript packages
const babel = require("gulp-babel"),
    stripDebug = require("gulp-strip-debug"),
    uglify = require("gulp-uglify");

// Vue packages
const browserify = require("browserify"),
    vueify = require("vueify-next"),
    source = require("vinyl-source-stream"),
    buffer = require("vinyl-buffer");

// Determine if gulp has been run with --production
const isProduction = minimist(process.argv.slice(2)).production !== undefined;

// Include browsersync when gulp has not been run with --production
let browserSync = undefined;

if (!isProduction) {
    browserSync = require("browser-sync").create();
}

// Declare plugin settings
const sassOutputStyle = isProduction ? "compressed" : "expanded",
    sassPaths = [ "bower_components", "node_modules" ],
    autoprefixerSettings = { remove: false, cascade: false },
    vuePaths = [ "./bower_components", "./node_modules", "./resources/components", "./resources/assets/js" ];

// Javascript files for the public site
const jsPublic = [ "resources/assets/js/app.js" ];

// Javascript libraries for the public site
const jsPublicLibs = [
    "node_modules/jquery/dist/jquery.js",
    "node_modules/popper.js/dist/umd/popper.js",
    "node_modules/bootstrap/dist/js/bootstrap.js",
    "node_modules/gsap/src/uncompressed/TweenMax.js",
    "node_modules/what-input/dist/what-input.js"
];

// Javascript files for the dashboard
const jsDashboard = [
    "resources/assets/js/dashboard.js"
];

// Javascript libraries for the dashboard
const jsDashboardLibs = [
    "node_modules/jquery/dist/jquery.js",
    "node_modules/popper.js/dist/umd/popper.js",
    "node_modules/bootstrap/dist/js/bootstrap.js",
    "node_modules/flatpickr/dist/flatpickr.js",
    "bower_components/Sortable/Sortable.js",
    "bower_components/list.js/dist/list.js",
    "bower_components/simplemde/dist/simplemde.min.js"
];

// CSS libraries for the dashboard
const cssDashboardLibs = [
    "node_modules/flatpickr/dist/flatpickr.css",
    "bower_components/simplemde/dist/simplemde.min.css",
    "bower_components/SpinKit/css/spinners/11-folding-cube.css"
];

// Paths to folders containing fonts that should be copied to public/fonts/
const fontPaths = [
    "resources/assets/fonts/**"
];

// Handle errors
function handleError(err) {
    log.error(err);
    this.emit("end");
}

// Process sass
function processSass(filename) {
    const css = gulp.src(`resources/assets/sass/${filename}.scss`)
        .pipe(plumber(handleError))
        .pipe(sassGlob())
        .pipe(sass({ outputStyle: sassOutputStyle, includePaths: sassPaths }))
        .pipe(postCSS([ autoprefixer(autoprefixerSettings) ]))
        .pipe(concat(`${filename}.css`))
        .pipe(gulp.dest("public/css/"));

    if (!isProduction) {
        css.pipe(browserSync.stream({ match: `**/${filename}.css` }));
    }

    return css;
}

// Process css
function processCSS(ouputFilename, inputFiles) {
    const css = gulp.src(inputFiles)
        .pipe(plumber(handleError))
        .pipe(postCSS([ autoprefixer(autoprefixerSettings) ]))
        .pipe(concat(`${ouputFilename}.css`));

    if (isProduction) {
        css.pipe(cleanCSS());
    }

    return css.pipe(gulp.dest("public/css/"));
}

// Process vue
function processVue(ouputFilename, inputFile) {
    const javascript = browserify({
        entries: [ inputFile ],
        paths: vuePaths
    }).transform("babelify")
        .transform(vueify)
        .bundle()
        .on("error", handleError)
        .pipe(source(`${ouputFilename}.js`))
        .pipe(buffer());

    if (isProduction) {
        javascript.pipe(stripDebug()).pipe(uglify().on("error", handleError));
    }

    return javascript.pipe(gulp.dest("public/js/"));
}

// Process javascript
function processJavaScript(ouputFilename, inputFiles, es6) {
    const javascript = gulp.src(inputFiles)
        .pipe(plumber(handleError))
        .pipe(concat(`${ouputFilename}.js`));

    if (es6) {
        if (isProduction) {
            javascript.pipe(babel()).pipe(stripDebug()).pipe(uglify());
        } else {
            javascript.pipe(babel());
        }
    } else if (isProduction) {
        javascript.pipe(stripDebug()).pipe(uglify());
    }

    return javascript.pipe(gulp.dest("public/js/"));
}

// Task for error page styles
gulp.task("sass-error", () => {
    return processSass("error");
});

// Task for public styles
gulp.task("sass-public", () => {
    return processSass("app");
});

// Task for dashboard styles
gulp.task("sass-dashboard", () => {
    return processSass("dashboard");
});

// Task for dashboard css libraries
gulp.task("css-dashboard-libs", () => {
    return processCSS("lib-dashboard", cssDashboardLibs);
});

// Task for public javascript
gulp.task("js-public", () => {
    return processVue("app", jsPublic);
});

// Task for public javascript libraries
gulp.task("js-public-libs", () => {
    return processJavaScript("lib", jsPublicLibs, false);
});

// Task for dashboard javascript
gulp.task("js-dashboard", () => {
    return processJavaScript("dashboard", jsDashboard, true);
});

// Task for dashboard javascript libraries
gulp.task("js-dashboard-libs", () => {
    return processJavaScript("lib-dashboard", jsDashboardLibs, false);
});

// Task to copy fonts
gulp.task("fonts", (done) => {
    gulp.src(fontPaths)
        .pipe(plumber(handleError))
        .pipe(gulp.dest("public/fonts/"));

    done();
});

// Task to watch files and run respective tasks when changes occur
gulp.task("watch", () => {
    const browserSyncReload = (done) => {
        browserSync.reload();
        done();
    };

    browserSync.init({
        logLevel: "silent",
        baseDir: "./public",
        notify: false,

        ghostMode: {
            clicks: false,
            forms: true,
            scroll: false
        }
    });

    gulp.watch([ "app/**/*.php", "routes/**/*.php", "resources/views/**/*.blade.php" ], gulp.series(browserSyncReload));
    gulp.watch([ "resources/assets/js/**/app.js", "resources/assets/js/mixins/**/*.js", "resources/components/**/*.vue" ], gulp.series("js-public", browserSyncReload));
    gulp.watch("resources/assets/js/**/dashboard.js", gulp.series("js-dashboard", browserSyncReload));
    gulp.watch("resources/assets/sass/**/*.scss", gulp.parallel("sass-public", "sass-dashboard", "sass-error"));
});

// Task to run non-development tasks
gulp.task("default", gulp.parallel(
    "sass-error",
    "sass-public",
    "sass-dashboard",
    "css-dashboard-libs",
    "js-public",
    "js-public-libs",
    "js-dashboard",
    "js-dashboard-libs",
    "fonts"
));
