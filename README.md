## How to deploy the API Management developer portal on WordPress and customize it?

This article shows how to deploy the API Management developer portal on WordPress. With the plugin, turn any WordPress site into a developer portal. You can take advantage of site capabilities in WordPress to customize and add features to your developer portal including localization, collapsible and expandable menus, custom stylesheets, file downloads, and more.

Follow the steps in this article to create a WordPress site on Azure App Service and configure the developer portal plugin on the WordPress site. Microsoft Entra ID is configured for authentication to the WordPress site and the developer portal.

To install the Azure API Management Developer Portal WordPress plugin, you need to follow the steps highlighted in this article below:
 
- [Documentation to deploy API management developer portal on Wordpress](https://aka.ms/apim-wordpress)
  
## How to download the latest installation file for APIM Developer portal Wordpress plugin?

Installation files for the developer portal WordPress plugin and customized WordPress theme from the [plugin repository](https://github.com/Azure/AzureAPIM-Wordpress-plugin/tree/main/dist). 

Download the following zip files from the dist folder in the repo:
1. [apim-devportal.zip](https://github.com/Azure/AzureAPIM-Wordpress-plugin/blob/main/dist/apim-devportal.zip) - Plugin file
2. [twentytwentyfour.zip](https://github.com/Azure/AzureAPIM-Wordpress-plugin/blob/main/dist/twentytwentyfour.zip) - Theme file
  
## Contributing

This project welcomes contributions and suggestions.  Most contributions require you to agree to a
Contributor License Agreement (CLA) declaring that you have the right to, and actually do, grant us
the rights to use your contribution. For details, visit https://cla.opensource.microsoft.com.

When you submit a pull request, a CLA bot will automatically determine whether you need to provide
a CLA and decorate the PR appropriately (e.g., status check, comment). Simply follow the instructions
provided by the bot. You will only need to do this once across all repos using our CLA.

This project has adopted the [Microsoft Open Source Code of Conduct](https://opensource.microsoft.com/codeofconduct/).
For more information see the [Code of Conduct FAQ](https://opensource.microsoft.com/codeofconduct/faq/) or
contact [opencode@microsoft.com](mailto:opencode@microsoft.com) with any additional questions or comments.

### Prerequisites

Before you begin, ensure you have the following prerequisites:

- **Node.js** & **NPM**: Install the latest version of Node.js from [nodejs.org](https://nodejs.org/).
- **PHP**: Ensure PHP is installed on your system. You can download it from [php.net](https://www.php.net/).
- **WordPress** & **Azure API Management**: A running instance of WordPress and instance of Azure API Management [set up properly with Microsoft Entra app](https://aka.ms/apim-wordpress)

### Tools used

- **Languages**: JavaScript, TypeScript, PHP
- **Packages**: `@wordpress/scripts` for running React components in WordPress

### Project structure

Notable folders and files you might need:

- `admin/` Contains PHP classes and UI for the WordPress admin screens
- `src/` Source code of front-end code for the plugin
  - `src/components/` Here you can find all components and utility functions that are shared between the different pages. Contains services for fetching data from the API.
  - `src/modules/` Contains the different widgets of the plugin
- `webpack.config.js` Webpack configuration for building the project

### How to run the project?

To work on the project you have two options:

#### On Azure

This approach has the best experience with authentication but has a slower dev experience.

1. Clone this repo.
1. Build the plugin (see below) and upload the generated plugin ZIP archive to your WordPress site.
1. Make changes to the code locally and repeat the step above.

#### Locally

You can run the project in your local WordPress instance. This approach has a smooth dev experience but have issues with authentication.

1. Setup XAMPP & WordPress locally.
1. Clone this repo and build the plugin (see below).
1. Create a Symbolic link (Symlink) to the plugin folder in your WordPress plugins folder `mklink /J "C:\xampp\htdocs\wp-content\plugins\apim-devportal" "C:\<this repo>"`
1. Activate the plugin in your WordPress admin panel.
1. Run `npm start` to start the development server.
1. Open your WordPress site and you should see the plugin running.
1. Make changes to the code and see the changes in real-time.
1. To make API requests that require authentication, you can hard-code user ID and SAS token in the `userHandlers.ts` file or manually set "Ocp-Apim-User-Id" and "Ocp-Apim-Sas-Token" cookies in the website.

After you are done with the changes, before pushing to the main branch, do not forget to build the archive and replace the one in the dist folder with the updated one.

### How to build the plugin?

To build a new version of the Azure API Management Developer Portal WordPress plugin, you need to run the following commands:
```bash
npm install
npm run build
npm run zip
```
This will generate a ZIP file with your plugin, which you can upload to your WordPress site.

### How to add new widgets?

To add a new widget to the plugin, you need to follow those steps:

- Add an entry point for the build tool to the `webpack.config.js` file.

- Add a new WordPress short code on the end of `apim-devportal.php` file. Here is an example for the APIs list widget:
  ```php
  /**
   * shortcode for apis list handling
   */
  function apis_list_widget() {
      return '<div id="apim-apis-list"></div>';
  }
  add_shortcode('APIs_List', 'apis_list_widget');
  
  function enqueue_apis_list_widget_script() {
      wp_enqueue_style( 'apis_list_widget-style', plugin_dir_url( __FILE__ ) . 'build/apisList.css' );
      wp_enqueue_script(
          'apis_list_widget-script',
          plugin_dir_url( __FILE__ ) . 'build/apisList.js',
          array('wp-element'),
          APIM_DEVPORTAL_VERSION,
          true // Load script in footer
      );
  
      // Passing data to javascript - Method 1
      $apim_service_name=get_option('apim_service_name', '');
      wp_add_inline_script( 'apis_list_widget-script', 'var apim_service_name = \'' . $apim_service_name . '\';' , 'before');
  
      // Pass data to JavaScript - Method 2
      // wp_localize_script('apis_list_widget-script', 'apimData', array(
      //     'apim_service_name' => $apim_service_name,
      //     'ajax_url' => admin_url('admin-ajax.php'),
      // ));
  }
  add_action('wp_enqueue_scripts', 'enqueue_apis_list_widget_script');
  ```

- Create a new module folder in the `src/modules` folder. Create a `index.js` file, which should have the following structure:

  ```javascript
  import {render} from "@wordpress/element"
  import App from "./App"
  
  const element = document.getElementById("<put the short code of the new widget here>")
  if (element) render(<App />, element)
  ```

- Create a `App.jsx` file in the same folder and start coding there.

## Trademarks

This project may contain trademarks or logos for projects, products, or services. Authorized use of Microsoft 
trademarks or logos is subject to and must follow 
[Microsoft's Trademark & Brand Guidelines](https://www.microsoft.com/en-us/legal/intellectualproperty/trademarks/usage/general).
Use of Microsoft trademarks or logos in modified versions of this project must not cause confusion or imply Microsoft sponsorship.
Any use of third-party trademarks or logos are subject to those third-party's policies.
