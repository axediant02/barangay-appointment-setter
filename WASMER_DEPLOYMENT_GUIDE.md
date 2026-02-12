# Wasmer Deployment Guide

## Table of Contents
1. [Introduction](#introduction)
2. [Wasmer Overview](#wasmer-overview)
3. [Installation](#installation)
4. [Core Concepts](#core-concepts)
5. [Quick Start](#quick-start)
6. [Creating WebAssembly Modules](#creating-webassembly-modules)
7. [Deployment Strategies](#deployment-strategies)
8. [Production Deployment](#production-deployment)
9. [Monitoring and Maintenance](#monitoring-and-maintenance)
10. [Troubleshooting](#troubleshooting)
11. [Best Practices](#best-practices)
12. [Advanced Topics](#advanced-topics)

---

## Introduction

Wasmer is a lightweight, fast, and secure WebAssembly (WASM) runtime that can run WebAssembly modules anywhere. This guide provides comprehensive instructions for deploying applications using Wasmer across different platforms and environments.

Whether you're deploying edge computing applications, serverless functions, or containerized microservices, Wasmer provides a unified runtime for WebAssembly execution with minimal overhead.

---

## Wasmer Overview

### What is Wasmer?

Wasmer is a standalone WebAssembly runtime that:
- **Runs WASM Anywhere**: Execute WebAssembly on Linux, macOS, Windows, and more
- **Language Agnostic**: Supports modules compiled from Rust, C/C++, Go, Python, and other languages
- **Fast Execution**: JIT and AOT (ahead-of-time) compilation for near-native performance
- **Secure**: Sandboxed execution environment prevents unauthorized access
- **Lightweight**: Minimal resource footprint suitable for edge and embedded devices
- **Portable**: Single compiled binary works across different platforms

### Key Features

- **Multiple Backends**: Cranelift, LLVM, and Singlepass compilers
- **Import/Export System**: Call functions between WASM and host
- **API Support**: JavaScript, Python, Rust, C/C++ bindings
- **WASI Support**: WebAssembly System Interface for I/O operations
- **Package Management**: Wasmer Package Manager for sharing modules

---

## Installation

### Windows Installation

#### Using Installer
1. Download the Windows installer from [wasmer.io](https://wasmer.io)
2. Run the installer executable
3. Follow the installation wizard
4. Add Wasmer to system PATH (usually automatic)
5. Verify installation:
```powershell
wasmer --version
wasmer-cli --help
```

#### Using Scoop
```powershell
scoop install wasmer
wasmer --version
```

#### Using Chocolatey
```powershell
choco install wasmer
wasmer --version
```

#### Manual Installation
1. Download pre-built binary from GitHub Releases
2. Extract to a directory (e.g., `C:\Program Files\Wasmer`)
3. Add to PATH environment variable
4. Open new PowerShell and verify:
```powershell
wasmer --version
```

### Linux Installation

#### Using curl (Recommended)
```bash
curl https://get.wasmer.io -sSfL | sh
source $HOME/.wasmer/wasmer.env
wasmer --version
```

#### Using apt (Ubuntu/Debian)
```bash
curl -fsSLO https://github.com/wasmerio/wasmer/releases/download/4.0.0/wasmer-linux-amd64.deb
sudo dpkg -i wasmer-linux-amd64.deb
wasmer --version
```

#### Using yum (RHEL/CentOS)
```bash
curl -fsSLO https://github.com/wasmerio/wasmer/releases/download/4.0.0/wasmer-linux-amd64.rpm
sudo yum install -y ./wasmer-linux-amd64.rpm
wasmer --version
```

#### Using Homebrew (macOS/Linux)
```bash
brew install wasmer
wasmer --version
```

### Docker Installation

#### Using Official Wasmer Docker Image
```bash
docker pull wasmerio/wasmer:latest
docker run -it wasmerio/wasmer:latest wasmer --version
```

#### Creating Custom Docker Image
```dockerfile
FROM wasmerio/wasmer:latest

# Copy your WASM modules
COPY ./modules /app/modules

# Set working directory
WORKDIR /app

# Default command
CMD ["wasmer", "--version"]
```

Build and run:
```bash
docker build -t my-wasmer-app:latest .
docker run my-wasmer-app:latest
```

---

## Core Concepts

### WebAssembly (WASM) Basics

**Binary Format**: WASM modules are compiled to binary format for efficiency.

**Sandboxed Execution**: Each WASM module runs in an isolated environment with no direct OS access.

**Linear Memory**: WASM modules use linear memory model for data storage.

**Imports/Exports**: Modules can import functions and export functionality.

### WASI (WebAssembly System Interface)

WASI provides standardized system-level APIs:
- File I/O operations
- Environment variables
- Command-line arguments
- Socket operations

Enable WASI in Wasmer:
```bash
wasmer run --dir=/tmp my-app.wasm
```

### Compilation Targets

**AOT (Ahead-of-Time) Compilation**:
```bash
wasmer compile module.wasm -o module.wasma
wasmer run module.wasma
```

**JIT (Just-In-Time) Compilation**:
```bash
wasmer run module.wasm  # Default - compiles at runtime
```

---

## Quick Start

### 1. Create a Simple WASM Module (Rust)

Create `hello.rs`:
```rust
#[no_mangle]
pub extern "C" fn add(a: i32, b: i32) -> i32 {
    a + b
}

#[no_mangle]
pub extern "C" fn greet(name: &str) -> String {
    format!("Hello, {}!", name)
}
```

Compile to WASM:
```bash
# Install wasm target
rustup target add wasm32-unknown-unknown

# Compile
rustc --target wasm32-unknown-unknown -O hello.rs -o hello.wasm
```

### 2. Run with Wasmer

```bash
wasmer run hello.wasm
```

### 3. Test the Module

Create `test.js`:
```javascript
const fs = require('fs');
const wasmer = require('@wasmerio/wasm-transformer');

const buffer = fs.readFileSync('./hello.wasm');
const module = new WebAssembly.Module(buffer);
const instance = new WebAssembly.Instance(module);

console.log(instance.exports.add(5, 3));  // Output: 8
```

Run with Node.js:
```bash
npm install @wasmerio/wasm-transformer
node test.js
```

---

## Creating WebAssembly Modules

### Using Rust

#### Project Setup
```bash
cargo new --lib my_wasm_app
cd my_wasm_app
```

#### Cargo.toml Configuration
```toml
[package]
name = "my_wasm_app"
version = "0.1.0"
edition = "2021"

[lib]
crate-type = ["cdylib"]

[dependencies]
wasm-bindgen = "0.2"

[profile.release]
opt-level = "z"
lto = true
```

#### Write Code
```rust
use wasm_bindgen::prelude::*;

#[wasm_bindgen]
pub fn add(a: i32, b: i32) -> i32 {
    a + b
}

#[wasm_bindgen]
pub struct Calculator {
    value: i32,
}

#[wasm_bindgen]
impl Calculator {
    #[wasm_bindgen(constructor)]
    pub fn new() -> Calculator {
        Calculator { value: 0 }
    }

    pub fn add(&mut self, n: i32) -> i32 {
        self.value += n;
        self.value
    }

    pub fn reset(&mut self) {
        self.value = 0;
    }
}
```

#### Build
```bash
rustup target add wasm32-unknown-unknown
cargo build --target wasm32-unknown-unknown --release
# Output: target/wasm32-unknown-unknown/release/my_wasm_app.wasm
```

### Using C/C++

#### Create source.c
```c
#include <stdio.h>

int add(int a, int b) {
    return a + b;
}

char* greet(char* name) {
    static char result[100];
    sprintf(result, "Hello, %s!", name);
    return result;
}
```

#### Compile with Emscripten
```bash
# Install Emscripten
git clone https://github.com/emscripten-core/emsdk.git
cd emsdk
./emsdk install latest
./emsdk activate latest
source ./emsdk_env.sh

# Compile
emcc source.c -O3 -o source.wasm -s WASM=1
```

### Using Go

#### Create main.go
```go
package main

import "syscall/js"

func add(this js.Value, args []js.Value) any {
    return args[0].Int() + args[1].Int()
}

func main() {
    js.Global().Set("add", js.FuncOf(add))
    select {}
}
```

#### Build
```bash
GOOS=js GOARCH=wasm go build -o app.wasm main.go
```

---

## Deployment Strategies

### Strategy 1: Standalone Execution

Deploy WASM modules directly with Wasmer runtime.

**Use Case**: Microservices, edge computing, serverless functions

**Steps**:
1. Compile application to WASM
2. Install Wasmer on target machine
3. Execute with Wasmer CLI
4. Optionally use wrapper scripts or systemd services

**Example Deployment Script**:
```bash
#!/bin/bash
set -e

DEPLOY_DIR="/opt/wasmer-apps"
APP_NAME="my-app"
MODULE_PATH="$DEPLOY_DIR/$APP_NAME/module.wasm"

# Create deployment directory
mkdir -p "$DEPLOY_DIR/$APP_NAME"

# Copy WASM module
cp build/app.wasm "$MODULE_PATH"

# Create systemd service
sudo tee /etc/systemd/system/$APP_NAME.service > /dev/null <<EOF
[Unit]
Description=Wasmer App - $APP_NAME
After=network.target

[Service]
Type=simple
User=wasmer
ExecStart=/usr/local/bin/wasmer run $MODULE_PATH
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

# Enable and start service
sudo systemctl daemon-reload
sudo systemctl enable $APP_NAME
sudo systemctl start $APP_NAME
```

### Strategy 2: Docker Container Deployment

Package WASM application in Docker container.

**Dockerfile**:
```dockerfile
FROM wasmerio/wasmer:latest

WORKDIR /app

# Copy WASM module and assets
COPY ./build/app.wasm ./
COPY ./assets ./assets
COPY ./config ./config

# Create non-root user
RUN groupadd -r wasmer && useradd -r -g wasmer wasmer
USER wasmer

# Expose port if running HTTP server
EXPOSE 8080

# Run application
ENTRYPOINT ["wasmer", "run", "app.wasm"]
```

**Build and Deploy**:
```bash
# Build image
docker build -t my-wasmer-app:1.0.0 .

# Run container
docker run -d \
  --name my-wasmer-app \
  -p 8080:8080 \
  --restart unless-stopped \
  my-wasmer-app:1.0.0

# Push to registry
docker tag my-wasmer-app:1.0.0 myregistry.azurecr.io/my-wasmer-app:1.0.0
docker push myregistry.azurecr.io/my-wasmer-app:1.0.0
```

### Strategy 3: Kubernetes Deployment

Deploy containerized WASM applications in Kubernetes.

**Deployment Manifest**:
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: wasmer-app
  namespace: default
spec:
  replicas: 3
  selector:
    matchLabels:
      app: wasmer-app
  template:
    metadata:
      labels:
        app: wasmer-app
    spec:
      containers:
      - name: wasmer-app
        image: myregistry.azurecr.io/my-wasmer-app:1.0.0
        imagePullPolicy: Always
        ports:
        - containerPort: 8080
          name: http
        env:
        - name: LOG_LEVEL
          value: "info"
        - name: CONFIG_PATH
          value: "/etc/config/app.conf"
        resources:
          requests:
            memory: "128Mi"
            cpu: "100m"
          limits:
            memory: "256Mi"
            cpu: "500m"
        livenessProbe:
          httpGet:
            path: /health
            port: 8080
          initialDelaySeconds: 10
          periodSeconds: 30
        readinessProbe:
          httpGet:
            path: /ready
            port: 8080
          initialDelaySeconds: 5
          periodSeconds: 10
        volumeMounts:
        - name: config
          mountPath: /etc/config
      volumes:
      - name: config
        configMap:
          name: wasmer-app-config
---
apiVersion: v1
kind: Service
metadata:
  name: wasmer-app-service
spec:
  type: LoadBalancer
  selector:
    app: wasmer-app
  ports:
  - protocol: TCP
    port: 80
    targetPort: 8080
---
apiVersion: v1
kind: ConfigMap
metadata:
  name: wasmer-app-config
data:
  app.conf: |
    server_port=8080
    log_level=info
    environment=production
```

**Deploy**:
```bash
kubectl apply -f deployment.yaml
kubectl get deployments
kubectl get pods
kubectl logs -f deployment/wasmer-app
```

### Strategy 4: Serverless/FaaS Deployment

Deploy WASM functions to serverless platforms.

**AWS Lambda with Wasmer**:
```bash
# Package for Lambda
zip -r lambda-package.zip app.wasm bootstrap.sh

# Upload to Lambda
aws lambda create-function \
  --function-name my-wasmer-function \
  --runtime provided.al2 \
  --role arn:aws:iam::ACCOUNT:role/lambda-role \
  --handler index.handler \
  --zip-file fileb://lambda-package.zip

# Test
aws lambda invoke \
  --function-name my-wasmer-function \
  --payload '{"action":"add","a":5,"b":3}' \
  response.json
```

**Cloudflare Workers with Wasmer**:
```javascript
export default async function handle(request) {
  const wasmModule = await import('path/to/module.wasm');
  const result = wasmModule.add(5, 3);
  return new Response(JSON.stringify({ result }));
}
```

---

## Production Deployment

### 1. Pre-deployment Checklist

- [ ] WASM module compiled with optimizations
- [ ] Performance benchmarks running
- [ ] Security audit completed
- [ ] Error handling implemented
- [ ] Logging configured
- [ ] Database migrations tested
- [ ] Rollback plan documented
- [ ] Monitoring alerts configured
- [ ] Load testing completed
- [ ] Documentation updated

### 2. Environment Configuration

Create `.env.production`:
```plaintext
# Server Configuration
SERVER_PORT=8080
SERVER_THREADS=4

# Database
DB_HOST=production-db.internal
DB_PORT=3306
DB_NAME=production_db
DB_USER=${SECRET_DB_USER}
DB_PASS=${SECRET_DB_PASS}

# Logging
LOG_LEVEL=info
LOG_FILE=/var/log/wasmer-app/app.log

# Performance
MAX_CONNECTIONS=100
REQUEST_TIMEOUT=30

# Security
CORS_ORIGIN=https://example.com
JWT_SECRET=${SECRET_JWT}
```

### 3. Resource Allocation

**CPU Configuration**:
```plaintext
Minimum: 1 CPU core per 1000 req/sec
Recommended: 2-4 CPU cores for production
```

**Memory Configuration**:
```plaintext
Minimum: 128 MB
Recommended: 256-512 MB
WASM Module: +50-100 MB per concurrent execution
```

**Disk Space**:
```plaintext
Application: 50-200 MB
Logs: 1-10 GB (depending on retention)
Temporary files: 1-5 GB
Total: 2-20 GB minimum
```

### 4. Deployment Process

#### Step-by-step Deployment

```bash
#!/bin/bash
set -e

VERSION=$1
DEPLOY_DIR="/opt/wasmer/app"
ARCHIVE="app-${VERSION}.tar.gz"

echo "[1/5] Validating deployment package..."
if [ ! -f "$ARCHIVE" ]; then
    echo "Error: Archive not found: $ARCHIVE"
    exit 1
fi

echo "[2/5] Creating backup..."
if [ -d "$DEPLOY_DIR" ]; then
    cp -r "$DEPLOY_DIR" "$DEPLOY_DIR.backup.$(date +%s)"
fi

echo "[3/5] Extracting new version..."
mkdir -p "$DEPLOY_DIR"
tar -xzf "$ARCHIVE" -C "$DEPLOY_DIR"

echo "[4/5] Running health checks..."
$DEPLOY_DIR/test.sh || {
    echo "Health checks failed, rolling back..."
    rm -rf "$DEPLOY_DIR"
    mv "$DEPLOY_DIR.backup."* "$DEPLOY_DIR"
    exit 1
}

echo "[5/5] Restarting service..."
sudo systemctl restart wasmer-app
sleep 5

if sudo systemctl is-active --quiet wasmer-app; then
    echo "Deployment successful!"
    exit 0
else
    echo "Service failed to start!"
    exit 1
fi
```

### 5. Health Checks & Verification

Create health check endpoint:
```c
// In your WASM module
#[wasm_bindgen]
pub fn health_check() -> String {
    json!({
        "status": "healthy",
        "version": env!("CARGO_PKG_VERSION"),
        "timestamp": current_timestamp(),
    }).to_string()
}
```

Test endpoint:
```bash
#!/bin/bash

API_URL="http://localhost:8080"
TIMEOUT=5

# Check health
echo "Checking health endpoint..."
response=$(curl -s -m $TIMEOUT "$API_URL/health")

if [ $? -eq 0 ]; then
    echo "Health check passed: $response"
    exit 0
else
    echo "Health check failed"
    exit 1
fi
```

### 6. Rollback Procedure

```bash
#!/bin/bash

DEPLOY_DIR="/opt/wasmer/app"
BACKUP_DIR="$DEPLOY_DIR.backup.$(ls -td $DEPLOY_DIR.backup.* | head -1)"

echo "Rolling back to: $BACKUP_DIR"

# Stop service
sudo systemctl stop wasmer-app

# Restore backup
rm -rf "$DEPLOY_DIR"
cp -r "$BACKUP_DIR" "$DEPLOY_DIR"

# Restart service
sudo systemctl start wasmer-app

echo "Rollback complete"
```

---

## Monitoring and Maintenance

### 1. Logging Configuration

Create logging wrapper:
```rust
#[cfg(all(target_os = "wasm32"))]
fn init_logger() {
    // WASI logging setup for WASM
}

#[cfg(not(target_os = "wasm32"))]
fn init_logger() {
    env_logger::Builder::from_default_env()
        .format_timestamp_millis()
        .init();
}
```

### 2. Performance Monitoring

**CPU Usage Monitoring**:
```bash
#!/bin/bash
# Monitor wasmer process CPU usage
while true; do
    top -b -n 1 | grep wasmer | awk '{print $9}'
    sleep 5
done
```

**Memory Usage Monitoring**:
```bash
#!/bin/bash
# Monitor memory consumption
watch -n 1 'ps aux | grep wasmer | grep -v grep | awk "{print \$6}" | awk "{sum+=\$1} END {print sum/1024 \" MB\"}"'
```

### 3. Error Tracking

Implement error logging:
```rust
use log::{error, warn, info};

#[wasm_bindgen]
pub fn process_request(data: &str) -> Result<String, JsValue> {
    match parse_input(data) {
        Ok(parsed) => {
            info!("Processing request: {:?}", parsed);
            Ok(JSON.stringify(result))
        },
        Err(e) => {
            error!("Parse error: {}", e);
            Err(JsValue::from(format!("Error: {}", e)))
        }
    }
}
```

### 4. Metrics Export

**Prometheus Integration**:
```bash
#!/bin/bash
# Export Wasmer metrics to Prometheus

METRICS_PORT=9090

cat > /tmp/metrics.sh << 'EOF'
#!/bin/bash
echo "# HELP wasmer_process_cpu_seconds_total CPU time"
echo "# TYPE wasmer_process_cpu_seconds_total counter"
ps aux | grep wasmer | awk 'NR==1 {print "wasmer_process_cpu_seconds_total " $7}'

echo "# HELP wasmer_process_resident_memory_bytes Memory usage"
echo "# TYPE wasmer_process_resident_memory_bytes gauge"
ps aux | grep wasmer | awk 'NR==1 {print "wasmer_process_resident_memory_bytes " $6*1024}'
EOF

chmod +x /tmp/metrics.sh
```

### 5. Regular Maintenance Tasks

**Weekly**:
- Review error logs
- Check disk space
- Verify backups
- Update security patches

**Monthly**:
- Performance analysis
- Dependency updates
- Security audit
- Load testing

**Quarterly**:
- Capacity planning
- Architecture review
- Disaster recovery test
- Documentation update

---

## Troubleshooting

### Common Issues & Solutions

#### Issue 1: Module Won't Load
```bash
# Symptom: "Error loading module"

# Solution 1: Verify WASM file
file app.wasm  # Should be "WebAssembly (wasm) binary module"

# Solution 2: Check Wasmer version compatibility
wasmer --version

# Solution 3: Use verbose mode
wasmer run --verbose app.wasm
```

#### Issue 2: Out of Memory
```bash
# Symptom: "Cannot allocate memory"

# Solution 1: Increase memory limit
wasmer run --memory-max 512 app.wasm

# Solution 2: Check memory usage
top -p $(pgrep wasmer)

# Solution 3: Profile application
WASMER_PROFILE=1 wasmer run app.wasm
```

#### Issue 3: Performance Degradation
```bash
# Symptom: Slow execution

# Solution 1: Use AOT compilation
wasmer compile app.wasm -o app.wasma
wasmer run app.wasma

# Solution 2: Change compiler backend
WASMER_COMPILER=cranelift wasmer run app.wasm

# Solution 3: Add JIT optimization
WASMER_JIT_THREADS=4 wasmer run app.wasm
```

#### Issue 4: WASI Permissions Error
```bash
# Symptom: "Permission denied" when accessing files

# Solution: Grant directory access
wasmer run --dir=/path/to/dir app.wasm

# Solution: Full access to specific directory
wasmer run --dir=/tmp app.wasm --data-dir=/tmp
```

#### Issue 5: Module Function Not Found
```bash
# Symptom: "Export not found: function_name"

# Debug: List exports
wasmer inspect app.wasm

# Ensure function is annotated correctly (Rust):
#[wasm_bindgen]
pub fn my_function() { }
```

---

## Best Practices

### 1. Security Best Practices

**Principle of Least Privilege**:
```bash
# Run as unprivileged user
useradd -r -s /bin/false wasmer
chown wasmer:wasmer /opt/wasmer/app
sudo -u wasmer wasmer run app.wasm
```

**Network Isolation**:
```bash
# Restrict network access
iptables -A OUTPUT -p tcp -m owner --uid-owner wasmer -j DROP
# Allow only specific destinations
iptables -A OUTPUT -p tcp -m owner --uid-owner wasmer -d 192.168.1.0/24 -j ACCEPT
```

**Resource Limits**:
```bash
# Set ulimits
ulimit -n 1024     # File descriptors
ulimit -u 100      # Processes
ulimit -v 524288   # Virtual memory (512MB)
```

**Secrets Management**:
```bash
# Use environment variables
export SECRET_KEY=$(aws secretsmanager get-secret-value --secret-id my-secret)
wasmer run app.wasm

# Or use config files with restricted permissions
sudo touch /etc/wasmer/secrets.conf
sudo chmod 600 /etc/wasmer/secrets.conf
```

### 2. Performance Optimization

**Compilation Optimization**:
```rust
// In Cargo.toml
[profile.release]
opt-level = "z"      # Optimize for size
lto = true           # Enable Link Time Optimization
codegen-units = 1    # Single codegen unit

[profile.optimize]
inherits = "release"
opt-level = 3        # Maximum optimization
```

**Runtime Optimization**:
```bash
# Use appropriate compiler backend
# Cranelift: Fast compilation, reasonably fast execution
WASMER_COMPILER=cranelift wasmer run app.wasm

# LLVM: Slower compilation, faster execution
WASMER_COMPILER=llvm wasmer run app.wasm

# Singlepass: Instant compilation, moderate speed
WASMER_COMPILER=singlepass wasmer run app.wasm
```

**Module Size Reduction**:
```bash
# Minimize WASM binary size
wasm-opt -Oz -o app_optimized.wasm app.wasm

# Strip debug symbols
wasm-strip app.wasm
```

### 3. Code Organization

**Modular Structure**:
```
src/
├── lib.rs                 # Main library
├── modules/
│   ├── math.rs
│   ├── string.rs
│   └── io.rs
├── error.rs              # Error handling
└── utils.rs              # Utility functions
```

**Clear Exports**:
```rust
#[wasm_bindgen]
pub fn add(a: i32, b: i32) -> i32 { a + b }

#[wasm_bindgen]
pub struct ComplexType { /* fields */ }

#[wasm_bindgen]
impl ComplexType {
    #[wasm_bindgen(constructor)]
    pub fn new() -> ComplexType { /* ... */ }
}
```

### 4. Testing Strategy

**Unit Testing**:
```rust
#[cfg(test)]
mod tests {
    use super::*;

    #[test]
    fn test_add() {
        assert_eq!(add(2, 3), 5);
    }
}
```

**Integration Testing**:
```bash
#!/bin/bash
# Test WASM module with Wasmer

TEST_WASM="test.wasm"
RESULT=$(wasmer run $TEST_WASM test_add)

if [ "$RESULT" = "5" ]; then
    echo "Test passed"
    exit 0
else
    echo "Test failed: expected 5, got $RESULT"
    exit 1
fi
```

### 5. Documentation

**README Structure**:
```markdown
# Project Name

## Overview
Brief description of what this WASM module does.

## Installation
How to build and run the module.

## API Reference
Exported functions and their signatures.

## Examples
Usage examples with code snippets.

## Performance
Benchmarks and optimization notes.

## Contributing
Guidelines for contributors.

## License
License information.
```

---

## Advanced Topics

### 1. Custom Host Functions

**Rust Implementation**:
```rust
use wasmer::{Function, Instance, Module, Store, Value};

fn main() -> Result<(), Box<dyn std::error::Error>> {
    let mut store = Store::default();
    
    // Define host function
    let host_add = Function::new(&mut store, |a: i32, b: i32| -> i32 {
        a + b
    });
    
    // Import into WASM module
    let import_object = imports! {
        "env" => {
            "add" => host_add,
        }
    };
    
    let module = Module::new(&store, wasm_bytes)?;
    let instance = Instance::new(&mut store, &module, &import_object)?;
    
    Ok(())
}
```

### 2. Dynamic Module Loading

```bash
#!/bin/bash
# Load WASM modules dynamically

MODULES_DIR="/opt/wasmer/modules"

for module in $MODULES_DIR/*.wasm; do
    echo "Loading: $module"
    wasmer run "$module" &
done

wait
```

### 3. Inter-Process Communication

**Using WASI Sockets**:
```bash
wasmer run --dir=/tmp --net app.wasm
```

**Using Files for IPC**:
```bash
wasmer run --dir=/var/run/wasmer app.wasm
```

### 4. Containerization Best Practices

**Multi-stage Build**:
```dockerfile
# Build stage
FROM rust:latest as builder
WORKDIR /src
COPY . .
RUN rustup target add wasm32-unknown-unknown
RUN cargo build --target wasm32-unknown-unknown --release

# Runtime stage
FROM wasmerio/wasmer:latest
COPY --from=builder /src/target/wasm32-unknown-unknown/release/*.wasm ./
CMD ["wasmer", "run", "app.wasm"]
```

### 5. Benchmarking

Create benchmark suite:
```bash
#!/bin/bash

MODULE="app.wasm"
ITERATIONS=1000

echo "Benchmarking $MODULE..."

time_start=$(date +%s%N)

for ((i=0; i<$ITERATIONS; i++)); do
    wasmer run "$MODULE" > /dev/null
done

time_end=$(date +%s%N)
duration=$((($time_end - $time_start) / 1000000))
avg=$((duration / ITERATIONS))

echo "Total time: ${duration}ms"
echo "Average per run: ${avg}ms"
echo "Throughput: $(echo "scale=2; 1000 / $avg" | bc) runs/sec"
```

---

## Conclusion

Wasmer provides a powerful platform for deploying WebAssembly applications with:
- **Portability** across multiple platforms
- **Security** through sandboxing
- **Performance** with multiple compiler backends
- **Flexibility** in deployment strategies

For more information, visit:
- Official Website: https://wasmer.io
- GitHub Repository: https://github.com/wasmerio/wasmer
- Documentation: https://docs.wasmer.io
- Community: https://wasmer.io/posts

---

## Additional Resources

- [WASM Specification](https://webassembly.org/)
- [WASI Specification](https://wasi.dev/)
- [Wasmer Package Registry](https://wasmer.io/packages)
- [WebAssembly Book](https://webassembly.org/docs/high-level/)
- [Wasmer GitHub Issues](https://github.com/wasmerio/wasmer/issues)

---

**Document Version**: 1.0  
**Last Updated**: February 12, 2026  
**Author**: Deployment Team
