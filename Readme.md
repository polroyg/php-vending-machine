# Vending Machine

Modelado de una máquina expendedora, manteniendo el estado durante su ejecución

## Table of Contents

- [Prerequisitos](#prerequisitos)
- [Desarrollo con VS Code](#desarrollo-con-vs-code)
- [Project Structure](#project-structure)
- [Development](#development)
- [Testing](#testing)
- [Contributing](#contributing)

# Prerequisitos
- VSCode
- Docker desktop


## Desarrollo con VS Code
1. Clonar el repositorio
2. Abre la carpeta en VSCode.
3. Arrancar el devcontainer.
4. ejecutar el script app.php que hay dentro del src

## Project Structure

```
/
├── .devcontainer/
│   └── devcontainer.json
│
├── src/
│   └── app.php
│   └── VendingMachine.php
│   │
│   └── Domain/
│   │   ├── Coin.php
│   │   ├── Item.php
│   │
│   └── Application/
│   │   ├── XXX
│   │
│   └── Infrastructure/
│       ├── XXX
│
├── tests/
├── README.md
└── composer.json
```

### Getting Started

```bash
php ./src/app.php
```
