# Vending Machine

Modelado de una máquina expendedora, manteniendo el estado durante su ejecución

## Table of Contents

- [Prerequisitos](#prerequisitos)
- [Desarrollo con VS Code](#desarrollo-con-vs-code)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
- [Arquitectura](#arquitectura-domain-driven-design-ddd)
- [Tareas pendientes](#tareas-pendientes)

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
├── challenge-sbe.md
├── README.md
└── Readme.md
```

### Getting Started

```bash
php ./src/app.php
```

## Arquitectura: Domain-Driven Design (DDD)

Este proyecto utiliza el enfoque de Domain-Driven Design (DDD) para organizar el código.

**Motivo:** DDD permite separar claramente la lógica de negocio (Dominio) de la infraestructura y la aplicación, facilitando la mantenibilidad, escalabilidad y comprensión del sistema. Así, cada parte del código tiene una responsabilidad bien definida y el modelo de dominio refleja fielmente las reglas y procesos del negocio de la máquina expendedora.


## Tareas pendientes

- [ ] Añadir tests para los servicios y la capa de infraestructura
- [ ] Crear interfaz de repositorio
- [ ] Crear excepciones propias y mejorar la gestión de errores
- [ ] Revisar y refactorizar algunos métodos
- [ ] Refactorizar código repetido
