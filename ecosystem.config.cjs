module.exports = {
  apps : [{
    name: "finovaste-ssr",
    script: "php",
    args: "artisan inertia:start-ssr",
    interpreter: "none",
    exec_mode: "fork",
    watch: false,
    max_restarts: 5,
    min_uptime: 10000,
  }]
};
