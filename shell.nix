{ pkgs ? import <nixpkgs> {} }:
  pkgs.mkShell {
    nativeBuildInputs = [ 
      pkgs.zola
      pkgs.php74 pkgs.php74Packages.composer
    ];
  }
