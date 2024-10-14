# Radio Playlist Generator

## Overview

The Radio Playlist Generator is a web-based application that allows users to create, edit, and display radio playlists. It consists of two main components: a builder interface (`builder.php`) for managing playlists, and a display interface (`index.php`) for presenting the playlists to listeners.

## Components

### 1. Builder Interface (builder.php)

The builder interface provides a secure, admin-only area for creating and editing playlists.

#### Features:

- Password-protected access (basic security implementation)
- Create new playlists
- Edit existing playlists
- Add, remove, and reorder playlist items
- Save playlists as JSON files

#### Usage:

1. Access the builder by navigating to `builder.php` in your web browser.
2. Log in using the predefined password.
3. Create a new playlist or select an existing one to edit.
4. Add, remove, or reorder items using the intuitive interface.
5. Save your changes.

### 2. Display Interface (index.php)

The display interface presents the created playlists to listeners in a user-friendly format.

#### Features:

- Loads and displays playlist data from JSON files
- Implements caching prevention to ensure the latest playlist is always shown
- Responsive design for various device sizes

#### Usage:

1. Access the display by navigating to `index.php` in your web browser.
2. The latest version of the playlist will be automatically loaded and displayed.

## Installation

1. Upload both `builder.php` and `index.php` to your web server.
2. Ensure the server has write permissions for the directory to allow JSON file creation and editing.
3. Modify the password in `builder.php` to secure the builder interface.

## Security Considerations

**Important Note**: This application uses a basic security implementation with a single hardcoded password. While it provides a minimal level of protection, it is not suitable for high-security environments or production use without further enhancements.

- Always use a strong, unique password for the builder interface.
- Regularly update the password to maintain security.
- Consider implementing additional security measures such as:
  - IP restrictions
  - Two-factor authentication
  - Proper user authentication system with hashed passwords
  - HTTPS to encrypt data in transit
- Regularly update and patch all components of your web server and application.

For production use or handling sensitive data, it is strongly recommended to consult with a security professional to implement more robust security measures.

## Customization

- The appearance of both interfaces can be customized by modifying the CSS within each file.
- Additional features can be added to the builder or display interfaces by extending the PHP and JavaScript code.

## Troubleshooting

- If playlists are not updating on the display interface, ensure that caching prevention is working correctly in `index.php`.
- If unable to save playlists in the builder interface, check server write permissions for the directory.

## Contributing

Contributions to improve the Radio Playlist Generator are welcome. Please submit pull requests or open issues on the project repository.

## License

MIT License

Copyright (c) 2024 Scott Saunders

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
