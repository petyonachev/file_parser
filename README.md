# file_parser

This is a small project that allows you to parse local and remote XML files and import the information to google sheets through <b>Google Sheets API</b>.

## Setup

### Local

Application can be setup locally wih Wamp/Xampp or whatever local setup you are using.

### Docker

Application can be run as docker executable. To set it up simply, pull repository and run <b>docker compose up</b>

## Configuration

To make sure that everything runs smoothly, there is small configuration required for testing and usage of the Google Sheets API.
 * Download your own Google API credentials json file and save it in the root directory of the project as <b>google-credentials.json</b>
 * Replace <b>.env</b> and <b>.env.test</b> SPREADSHEET_ID and SHEET_NAME with your personal spreadsheet values. Keep in that this project uses 1 spreadsheet when running the command, and a different test spreadsheet for testing. You will need to create 2 spreadsheets and give your <b>google-credentials.json</b> email access to both

##Usage

You can now create your own commands that will use the already defined services for reading xml files and updating Google Sheets. To run the predefined command use <b>bin/console app:convert-coffee-catalog --location --filepath</b>.
 * Location parameter expects either <b>local</b> or <b>remote</b> depending on whether the file is stored locally or is a remote file.
 * Filepath parameter should contain either the full path to the file if the file is stored locally, or link to the raw XML data if the file is stored remotely. this project can only handle reading remote files that are XML or links that contain only XML data and no HTML.

### Known Issues

Composer sometimes gets stuck while installing google/api-client. I don't know why that is happening, but sometimes that is fixed by deleting vendor/google and running composer install again. So far I haven't found a solution that works fully. It seems to be getting stuck while running rm -rf command on some cached folder.