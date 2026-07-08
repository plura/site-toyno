# Toyno — Site

WordPress site for [toyno.com](https://toyno.com).

## Structure

| Folder | Description | Remote path |
|--------|-------------|-------------|
| `theme/` | Divi child theme | `/wp/wp-content/themes/Divi-child/` |
| `plugin/` | Custom site plugin | `/wp/wp-content/plugins/toyno/` |

## SFTP

Copy `.vscode/sftp.json.example` to `.vscode/sftp.json` and fill in the credentials.
The extension [SFTP](https://marketplace.visualstudio.com/items?itemName=Natizyskunk.sftp) is used to sync files to the server.

Connection uses explicit FTPS (`secure: true`) — plain FTP does not work on this host.
