# Contributing

**Number 1 Rule: Keep the PRs small. They should have one and only one purpose. The smaller the better.**

## Environment Setup

If you'd like a relatively simple way to set up your own development environment for contributing to this project, here are some instructions using Docker containers.

### Method 1: Docker Compose (Recommended)

1. Clone this repo or fork it and clone your fork. Install [Docker Desktop](https://www.docker.com/products/docker-desktop/) (includes Docker Compose v2)
2. In the new cloned directory, cd into `src` directory, make copies of `.example` files and remove the suffixes on them. Change the following in `config.php` and save:

```php
  $servername = "mhct-db";
  $username = "admin";
  $password = "admin"; // Change these for better security
  $dbname = "mhhunthelper";
```

3. CD back to the root directory, run `docker build up -d` (this will take a while as it needs to download the database and images)
   1. :warning: These images are likely far out of date. It's recommended to build these yourself from the [m-h-c-t/mhct-db-docker](https://github.com/m-h-c-t/mhct-db-docker)
4. You should now be able to access the local server via http://127.0.0.1 or http://localhost on your browser, connect to the database via port 3306, and modify the code on your host machine. Changes to the code are reflected immediately via volume mounts.

To add other databases, uncomment them in `docker-compose.yml` and run `docker compose up -d` again

### Method 2: Podman (Docker Alternative)

Podman supports Docker Compose files natively:

```bash
podman compose up -d
```

Follow the same configuration steps as Method 1.

## Debugging

You can debug PHP files using Visual Studio Code with Xdebug. Follow these steps:

### 1. Install PHP Debug Extension

Install the recommended extensions for this repo when prompted by VSCode.

### 2. Install Browser Extension

Install a browser extension to easily trigger Xdebug sessions:

- [Chrome Xdebug Extension](https://chromewebstore.google.com/detail/xdebug-chrome-extension/oiofkammbajfehgpleginfomeppgnglk?hl=en)
- [Firefox Xdebug Helper](https://addons.mozilla.org/en-US/firefox/addon/xdebug-helper-for-firefox/)

Set the IDE key to **PhpStorm** in the extension settings. The XDEBUG trigger header is configured to PHPSTORM in this repo's dockerfile.

### 3. Start Debugging

1. Ensure your Docker containers are running (`docker compose up -d`)
2. Set breakpoints in your PHP files by clicking in the gutter next to line numbers
3. In VS Code, go to the Run and Debug view (Ctrl+Shift+D) and select "Listen for Xdebug"
4. Click the green play button to start listening for Xdebug connections
5. In your browser, enable the Xdebug extension (click the icon and select "Debug")
6. Navigate to your local site (http://localhost)
7. VS Code should stop at your breakpoints, allowing you to inspect variables, step through code, and use the debug console

### Troubleshooting

- If breakpoints aren't being hit, verify the `pathMappings` in launch.json match your Docker volume configuration
- Check that port 9003 isn't blocked by your firewall
- Ensure the Xdebug browser extension is enabled and set to "Debug" mode
