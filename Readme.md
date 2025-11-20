# Vending Machine

Modelado de una máquina expendedora, manteniendo el estado durante su ejecución

## Table of Contents

- [Prerequisitos](#prerequisitos)
- [Desarrollo con VS Code](#desarrollo-con-vs-code)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
- [Arquitectura](#arquitectura-domain-driven-design-ddd)
- [Extras](#extras)
- [Despliegue con docker](#despliegue-con-docker)

# Prerequisitos
- VSCode
- Docker desktop


## Desarrollo con VS Code
1. Clonar el repositorio
2. Abre la carpeta en VSCode.
3. Inicia el entorno de desarrollo (devcontainer).
4. Ejecutar el script app.php ubicado en la carpeta `src`

## Project Structure

```
/
├── .devcontainer/
│   └── devcontainer.json
│
├── src/
│   ├── app.php
│   ├── Application.php
│   ├── command.php
│   │
│   ├── Domain/
│   │   ├── CashBox.php
│   │   ├── CashBoxItem.php
│   │   ├── Coin.php
│   │   ├── Item.php
│   │   ├── Transaction.php
│   │   └── VendingMachine.php
│   │
│   ├── Infrastructure/
│   │   ├── ConsoleIO.php
│   │   ├── JsonStorage.php
│   │   └── repositories/
│   │       ├── CashBoxItemJsonRepository.php
│   │       └── ItemJsonRepository.php
│   │
│   ├── Services/
│   │   ├── CustomerService.php
│   │   └── MaintenanceService.php
│   │
│   └── data/
│       ├── cashbox.json
│       └── items.json
│
├── tests/
│   ├── Domain/
│   │   ├── CashBoxItemTest.php
│   │   ├── CashBoxTest.php
│   │   ├── CointTest.php
│   │   ├── TransactionTest.php
│   │   └── VendorMachineTest.php
│   │
│   ├── Infrastructure/
│   │   ├── ConsoleIOTest.php
│   │   ├── JsonStorageTest.php
│   │   └── repositories/
│   │       ├── CashBoxItemJsonRepositoryTest.php
│   │       └── ItemJsonRepositoryTest.php
│   │
│   └── Services/
│
├── vendor/
│   └── ... (dependencias de Composer)
│
├── composer.json
├── phpstan.neon
├── phpunit.xml
└── Readme.md
```


## Getting Started
### Modo comando de terminal
En modo terminal se debe ejecutar, dentro de la carpeta `src` el comando:
```bash
./command.php ARG1 ARG2 ARG3
```
o
```bash
./command.php "ARG1"
```
Siempre se espera la acción a realizar en última posición dentro de los argumentos.

Las acciones disponibles para casos de uso de cliente son:
- GET-{CODIGO_ARTICULO} -> Comprar un artículo
- RETURN-COIN -> Devolver las monedas introducidas

Antes de la acción se espera las monedas a introducir en la máquina.
`./command.php "1, 0.25, 0.25, GET-SODA"`
`./command.php "1, 0.25, 0.25, RETURN-COIN"`
`./command.php 1 0.25 0.25 GET-SODA`
`./command.php 1 0.25 0.25 RETURN-COIN`

Las acciones disponibles para casos de uso siempre terminan con la acción `SERVICE` y se le puede pasar un argumento más al principio, indicando lo que queremos ver:
- COINS -> Para ver el estado del cambio/monedas
- ITEMS -> Para ver el estado del stock/productos
`./command.php "COINS, SERVICE"`
`./command.php "ITEMS, SERVICE"`
`./command.php "SERVICE"`
`./command.php COINS SERVICE`
`./command.php ITEMS SERVICE`
`./command.php SERVICE`
Esta última opción muestra el cambio y el stock

### Modo interactivo en terminal
Para arrancar el modo interactivo del terminal debes ejecutar el comando
```bash
./app.php
```
dentro de la carpeta `src` y podrás interactuar con la aplicación a través de los menús y opciones que muestra la consola.

## Arquitectura: Domain-Driven Design (DDD)

Este proyecto utiliza el enfoque de Domain-Driven Design (DDD) para organizar el código.

**Motivo:** DDD permite separar claramente la lógica de negocio (Dominio) de la infraestructura y la aplicación, facilitando la mantenibilidad, escalabilidad y comprensión del sistema. Así, cada parte del código tiene una responsabilidad bien definida y el modelo de dominio refleja fielmente las reglas y procesos del negocio de la máquina expendedora.


## Despliegue con docker
Utilizando el Dockerfile que hay en la raíz, se puede desplegar directamente con docker y ejecutar los comandos anteriores para utilizar la aplicación.
Los pasos a seguir son:
**Construir la imagen**
`docker build -t vending-php .` ejecutar en la raíz del proyecto

**Iniciar el contenedor con un shell interactivo**
`docker run --rm -it -v "$PWD":/app -w /app/src vending-php bash`

## Próximos pasos

- [ ] Añadir tests para los servicios y la capa de infraestructura
- [ ] Crear interfaz de repositorio
- [ ] Crear excepciones propias y mejorar la gestión de errores
- [ ] Revisar y refactorizar algunos métodos
- [ ] Refactorizar código repetido
- [x] Refactorizar command.php
