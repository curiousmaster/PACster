![alt text](https://github.com/curiousmaster/PACster/blob/main/htdocs/admin/images/pacster.png?raw=true)

# PACster

PACster is a web-based tool for testing Proxy Auto-Config (PAC) files. It allows users to select multiple PAC files, test URLs against these files, and view the routing results. The tool provides a user-friendly interface and supports various functionalities, including downloading test results as CSV files.

## Features

- **Multiple PAC File Selection**: Users can select one or more PAC files for testing.
- **URL Testing**: Enter multiple URLs to test against the selected PAC files.
- **Dynamic Results Display**: View results in a structured table that shows the PAC file used, tested URL, resolved host, and routing decision.
- **CSV Export**: Download the test results as a CSV file for further analysis.
- **Commit Information**: Display last commit information for the selected PAC files.

## Installation

1. Clone this repository to your local machine:
   ```bash
   git clone https://github.com/curiousmaster/PACster.git
   ```

2. Navigate to the project directory:
   ```bash
   cd PACster
   ```

3. Edit your server environment to serve PHP files.

4. Place the project files in the appropriate directory on your server.

5. Ensure that the server has access to the necessary PAC files and that the `BASE_PATH` is correctly set in `admin/config.php`.

## Usage

1. Open PACster in your web browser.
2. Select one or more PAC files from the dropdown menu.
3. Enter the URLs you want to test in the provided textarea.
4. Click the "Test" button to run the tests.
5. View the results in the table below and download them as a CSV file if needed.

## Contributing

Contributions are welcome! If you have suggestions for improvements or find bugs, please submit an issue or create a pull request.

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## Acknowledgments

- [Proxy Auto-Config Documentation](https://developer.mozilla.org/en-US/docs/Web/HTTP/Proxy_Auto-Configuration_File)
